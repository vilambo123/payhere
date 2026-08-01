/**
 * Google Apps Script: Check Bad Debt (CheckMyInfo API)
 *
 * Use in a sheet with either:
 * - Inquiry layout: column "IC Number" → fills "Bad Debt Check" column
 * - Remarks layout: column "IC" → fills "Remarks" column (Date, Name, Phone, Ads Tag, Loan Amount, IC, Jacky, James, Jessica, Remarks, Payslip)
 *
 * Add this file to your Apps Script project (same project as GoogleSheet_InquirySync.gs or standalone).
 * Menu "Bad Debt" appears on spreadsheet open.
 */

const BAD_DEBT_API_URL = 'https://www.checkmyinfo.co/api/check-bad-debts';
const BAD_DEBT_DELAY_MS = 300; // delay between API calls — 300 ms is sufficient; increase only if you hit rate-limit errors

/**
 * Call CheckMyInfo API to check bad debt for one NRIC.
 * @param {string} nric - IC/NRIC number
 * @returns {string} "w:true, ri:false, bad" or "w:false, ri:false, good" if success; "failed to check" if not
 */
function checkBadDebtApi(nric) {
  if (!nric || String(nric).trim() === '') return '';
  const payload = 'nric=' + encodeURIComponent(String(nric).trim());
  try {
    const response = UrlFetchApp.fetch(BAD_DEBT_API_URL, {
      method: 'post',
      contentType: 'application/x-www-form-urlencoded',
      payload: payload,
      muteHttpExceptions: true
    });
    const text = response.getContentText();
    let json;
    try {
      json = JSON.parse(text);
    } catch (e) {
      return 'failed to check';
    }
    if (json.success === true && json.data) {
      const w = json.data.w === true;
      const ri = json.data.ri === true;
      const label = (!w && !ri) ? 'good' : 'bad';
      return 'w:' + w + ', ri:' + ri + ', ' + label;
    }
    return 'failed to check';
  } catch (e) {
    return 'failed to check';
  }
}

/**
 * Returns true if the text already contains a bad-debt check result (so we can skip re-checking).
 */
function alreadyHasBadDebtResult(text) {
  if (!text || String(text).trim() === '') return false;
  const s = String(text).toLowerCase();
  return s.indexOf('w:true') !== -1 || s.indexOf('w:false') !== -1 || s.indexOf('failed to check') !== -1;
}

/**
 * Find column index (1-based) by header text in the first row
 */
function findColumnByHeader(sheet, headerText) {
  const headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
  const t = String(headerText).toLowerCase();
  for (let i = 0; i < headers.length; i++) {
    if (String(headers[i]).toLowerCase() === t) return i + 1;
  }
  return -1;
}

/**
 * Ensure "Bad Debt Check" column exists; add it if missing. Returns 1-based column index.
 */
function ensureBadDebtCheckColumn(sheet) {
  const col = findColumnByHeader(sheet, 'Bad Debt Check');
  if (col > 0) return col;
  const lastCol = sheet.getLastColumn();
  sheet.getRange(1, lastCol + 1).setValue('Bad Debt Check');
  sheet.getRange(1, lastCol + 1).setFontWeight('bold');
  return lastCol + 1;
}

/**
 * For each row that has an IC number and no Bad Debt Check result, call the API and fill the result.
 * Uses column headers "IC Number" and "Bad Debt Check" (adds Bad Debt Check column if missing).
 */
function checkBadDebtsForSheet() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  const lastRow = sheet.getLastRow();
  if (lastRow < 2) {
    SpreadsheetApp.getUi().alert('No data rows. Pull inquiries first.');
    return;
  }

  const icCol = findColumnByHeader(sheet, 'IC Number');
  if (icCol < 0) {
    SpreadsheetApp.getUi().alert('Column "IC Number" not found.');
    return;
  }

  const resultCol = ensureBadDebtCheckColumn(sheet);

  let checked = 0;
  let skipped = 0;

  for (let r = 2; r <= lastRow; r++) {
    const ic = sheet.getRange(r, icCol).getValue();
    const existing = sheet.getRange(r, resultCol).getValue();
    const icStr = ic ? String(ic).trim() : '';
    const hasResult = existing !== null && existing !== undefined && String(existing).trim() !== '';

    if (icStr === '') continue;
    if (hasResult) {
      skipped++;
      continue;
    }

    const result = checkBadDebtApi(icStr);
    const cell = sheet.getRange(r, resultCol);
    cell.setValue(result);
    
    // Color the cell red if result contains "bad"
    if (result.toLowerCase().indexOf('bad') !== -1) {
      cell.setBackground('#fee2e2'); // Light red background
      cell.setFontColor('#991b1b'); // Dark red text
      cell.setFontWeight('bold');
    } else if (result.toLowerCase().indexOf('good') !== -1) {
      cell.setBackground('#d1fae5'); // Light green background
      cell.setFontColor('#065f46'); // Dark green text
      cell.setFontWeight('bold');
    } else {
      cell.setBackground('#fef3c7'); // Light yellow for "failed to check"
      cell.setFontColor('#92400e'); // Dark yellow text
    }
    
    checked++;
    Utilities.sleep(BAD_DEBT_DELAY_MS);
  }

  SpreadsheetApp.getUi().alert('Done. Checked: ' + checked + ', Skipped (already had result): ' + skipped + '.');
}

/**
 * For sheets with headers: Date, Name, Phone Number, Ads Tag, Loan Amount, IC, Jacky, James, Jessica, Remarks, Payslip
 * Checks IC via API and adds the bad debt status into the Remarks column.
 * - Rows with no IC are skipped.
 * - If Remarks already has text, the status is appended with " | "; otherwise Remarks is set to the status.
 *
 * Resumable: stays within Apps Script's ~6-min execution cap by processing a chunk per run,
 * saving progress to Document Properties, then scheduling a trigger to continue automatically.
 */
const CRS_MAX_RUN_MS    = 5 * 60 * 1000; // stop after ~5 min, leaving cleanup headroom
const CRS_RESUME_MS     = 60 * 1000;     // gap between chunks
const CRS_HANDLER       = '_resumeCheckBadDebtsForRemarksSheet';
const CRS_PROPS_KEYS    = ['crs_ssId','crs_sheetId','crs_lastRow','crs_checked','crs_skipIc','crs_skipDone'];

function checkBadDebtsForRemarksSheet() {
  _runCheckBadDebtsForRemarksSheet(false);
}

/** Cancel an in-progress check — removes the trigger and clears saved state. */
function cancelCheckBadDebtsForRemarksSheet() {
  _crsCleanupTrigger();
  _crsClearProgress();
  try { SpreadsheetApp.getUi().alert('Check cancelled. Progress cleared and resume trigger removed.'); } catch (e) {}
}

/** Trigger handler — no interactive UI allowed here. */
function _resumeCheckBadDebtsForRemarksSheet() {
  _runCheckBadDebtsForRemarksSheet(true);
}

function _runCheckBadDebtsForRemarksSheet(fromTrigger) {
  const startMs = Date.now();
  const props   = PropertiesService.getDocumentProperties();

  // Resolve target sheet (store IDs on first call so the trigger can reopen it)
  let sheet;
  if (fromTrigger) {
    const ssId    = props.getProperty('crs_ssId');
    const sheetId = props.getProperty('crs_sheetId');
    if (!ssId || !sheetId) { _crsCleanupTrigger(); return; }
    const ss = SpreadsheetApp.openById(ssId);
    sheet = ss.getSheets().filter(function (s) { return String(s.getSheetId()) === sheetId; })[0];
    if (!sheet) { _crsCleanupTrigger(); _crsClearProgress(); return; }
  } else {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    sheet = ss.getActiveSheet();
    // Store IDs so the trigger can find this exact sheet later
    props.setProperties({ 'crs_ssId': ss.getId(), 'crs_sheetId': String(sheet.getSheetId()) }, false);
  }

  const lastRow = sheet.getLastRow();
  if (lastRow < 2) {
    if (!fromTrigger) { try { SpreadsheetApp.getUi().alert('No data rows in this sheet.'); } catch (e) {} }
    _crsCleanupTrigger(); _crsClearProgress(); return;
  }

  const icCol = findColumnByHeader(sheet, 'IC');
  if (icCol < 0) {
    if (!fromTrigger) { try { SpreadsheetApp.getUi().alert('Column "IC" not found. Ensure the sheet has a header row with "IC".'); } catch (e) {} }
    _crsCleanupTrigger(); _crsClearProgress(); return;
  }

  const remarksCol = findColumnByHeader(sheet, 'Remarks');
  if (remarksCol < 0) {
    if (!fromTrigger) { try { SpreadsheetApp.getUi().alert('Column "Remarks" not found. Ensure the sheet has a header row with "Remarks".'); } catch (e) {} }
    _crsCleanupTrigger(); _crsClearProgress(); return;
  }

  // Batch-read both columns once (much faster than getValue per cell)
  const ics     = sheet.getRange(2, icCol,      lastRow - 1, 1).getValues();
  const remarks = sheet.getRange(2, remarksCol, lastRow - 1, 1).getValues();

  // Restore counters and row cursor; on a fresh run everything starts at 0 / row 2
  let startRow = parseInt(props.getProperty('crs_lastRow') || '1', 10) + 1;
  if (startRow < 2) startRow = 2;
  let checked             = parseInt(props.getProperty('crs_checked')  || '0', 10);
  let skippedNoIc         = parseInt(props.getProperty('crs_skipIc')   || '0', 10);
  let skippedAlreadyDone  = parseInt(props.getProperty('crs_skipDone') || '0', 10);

  // Write buffer — flush to sheet every FLUSH_EVERY rows to cut Sheets API calls
  const FLUSH_EVERY = 20;
  const pendingWrites = []; // { row, value, bg, fc, bold }

  function flushPending() {
    pendingWrites.forEach(function (w) {
      const cell = sheet.getRange(w.row, remarksCol);
      cell.setValue(w.value);
      if (w.bg)   { cell.setBackground(w.bg); cell.setFontColor(w.fc); }
      if (w.bold) { cell.setFontWeight('bold'); }
    });
    SpreadsheetApp.flush(); // send all queued writes to Sheets in one round-trip
    pendingWrites.length = 0;
  }

  let r;
  for (r = startRow; r <= lastRow; r++) {

    // Time check — flush buffer, save progress, and schedule a resume
    if (Date.now() - startMs > CRS_MAX_RUN_MS) {
      flushPending();
      props.setProperties({
        'crs_lastRow':  String(r - 1),
        'crs_checked':  String(checked),
        'crs_skipIc':   String(skippedNoIc),
        'crs_skipDone': String(skippedAlreadyDone)
      }, false);
      _crsScheduleResume();
      try {
        SpreadsheetApp.getActive().toast(
          'Pausing at row ' + (r - 1) + ' — resuming automatically in ~1 min.\n' +
          'Checked so far: ' + checked + ' | Skipped (no IC): ' + skippedNoIc + ' | Already done: ' + skippedAlreadyDone,
          'Bad Debt Check', 10
        );
      } catch (e) {}
      return;
    }

    const ic          = ics[r - 2][0];
    const existing    = remarks[r - 2][0];
    const icStr       = ic !== null && ic !== undefined ? String(ic).trim() : '';
    const existingStr = existing !== null && existing !== undefined ? String(existing).trim() : '';

    if (icStr === '') { skippedNoIc++; continue; }
    if (alreadyHasBadDebtResult(existingStr)) { skippedAlreadyDone++; continue; }

    const result     = checkBadDebtApi(icStr);
    const newRemarks = existingStr === '' ? result : existingStr + ' | ' + result;

    let bg = '', fc = '', bold = false;
    if (result.toLowerCase().indexOf('bad') !== -1) {
      bg = '#fee2e2'; fc = '#991b1b'; bold = true;
    } else if (result.toLowerCase().indexOf('good') !== -1) {
      bg = '#d1fae5'; fc = '#065f46'; bold = true;
    } else if (result.toLowerCase().indexOf('failed') !== -1) {
      bg = '#fef3c7'; fc = '#92400e';
    }
    pendingWrites.push({ row: r, value: newRemarks, bg: bg, fc: fc, bold: bold });

    // Flush buffer every FLUSH_EVERY API calls to avoid losing too much work on timeout
    if (pendingWrites.length >= FLUSH_EVERY) flushPending();

    checked++;
    Utilities.sleep(BAD_DEBT_DELAY_MS);
  }

  // Flush any remaining buffered writes
  flushPending();

  // All rows processed — clean up and report
  _crsCleanupTrigger();
  _crsClearProgress();

  const summary = 'Done! Checked: ' + checked +
    ' | Skipped (no IC): ' + skippedNoIc +
    ' | Skipped (already checked): ' + skippedAlreadyDone;

  if (!fromTrigger) {
    try { SpreadsheetApp.getUi().alert(summary); } catch (e) {}
  } else {
    try { SpreadsheetApp.getActive().toast(summary, 'Bad Debt Check', 10); } catch (e) {}
    Logger.log(summary);
  }
}

function _crsScheduleResume() {
  _crsCleanupTrigger();
  ScriptApp.newTrigger(CRS_HANDLER).timeBased().after(CRS_RESUME_MS).create();
}

function _crsCleanupTrigger() {
  ScriptApp.getProjectTriggers().forEach(function (t) {
    if (t.getHandlerFunction() === CRS_HANDLER) ScriptApp.deleteTrigger(t);
  });
}

function _crsClearProgress() {
  const props = PropertiesService.getDocumentProperties();
  CRS_PROPS_KEYS.forEach(function (k) { props.deleteProperty(k); });
}

/**
 * Recheck all "good" inquiries and highlight in yellow if they've changed to "bad".
 * For inquiry sheets with "Bad Debt Check" column.
 */
function recheckGoodInquiries() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getActiveSheet();
  const lastRow = sheet.getLastRow();
  if (lastRow < 2) {
    SpreadsheetApp.getUi().alert('No data rows.');
    return;
  }

  const icCol = findColumnByHeader(sheet, 'IC Number');
  if (icCol < 0) {
    SpreadsheetApp.getUi().alert('Column "IC Number" not found.');
    return;
  }

  const resultCol = findColumnByHeader(sheet, 'Bad Debt Check');
  if (resultCol < 0) {
    SpreadsheetApp.getUi().alert('Column "Bad Debt Check" not found. Run initial check first.');
    return;
  }

  let rechecked = 0;
  let changedToBad = 0;
  const changedRows = [];

  for (let r = 2; r <= lastRow; r++) {
    const ic = sheet.getRange(r, icCol).getValue();
    const existing = sheet.getRange(r, resultCol).getValue();
    const icStr = ic ? String(ic).trim() : '';
    const existingStr = existing ? String(existing).toLowerCase() : '';

    // Only recheck rows that currently show "good"
    if (icStr === '' || existingStr.indexOf('good') === -1) continue;

    const newResult = checkBadDebtApi(icStr);
    rechecked++;

    // Check if status changed from "good" to "bad"
    if (newResult.toLowerCase().indexOf('bad') !== -1) {
      const cell = sheet.getRange(r, resultCol);
      cell.setValue(newResult);
      
      // Highlight in yellow to indicate it changed
      cell.setBackground('#fef08a'); // Yellow background
      cell.setFontColor('#854d0e'); // Dark yellow/brown text
      cell.setFontWeight('bold');
      
      changedToBad++;
      changedRows.push(r);
    }

    Utilities.sleep(BAD_DEBT_DELAY_MS);
  }

  let message = 'Recheck complete!\n\n';
  message += 'Total rechecked: ' + rechecked + '\n';
  message += 'Changed to BAD: ' + changedToBad;
  
  if (changedToBad > 0) {
    message += '\n\nRows with changes (highlighted in yellow): ' + changedRows.join(', ');
  }

  SpreadsheetApp.getUi().alert(message);
}

/**
 * Recheck all "good" inquiries in remarks sheet and highlight in yellow if changed to "bad".
 * For sheets with "IC" and "Remarks" columns.
 *
 * Resumable: Apps Script execution is capped at ~6 minutes. This run processes as many rows
 * as it can within MAX_RUN_MS, saves progress to Document Properties, and schedules a
 * time-based trigger to continue ~1 minute later until done.
 */
const RGIR_MAX_RUN_MS = 5 * 60 * 1000; // stop ~5 min in, leaving headroom for cleanup
const RGIR_RESUME_AFTER_MS = 60 * 1000; // wait between chunks
const RGIR_RESUME_HANDLER = '_resumeRecheckGoodInquiriesRemarks';
const RGIR_PROPS_KEYS = ['rgir_ssId', 'rgir_sheetId', 'rgir_lastRow', 'rgir_rechecked', 'rgir_changed', 'rgir_changedRows'];

function recheckGoodInquiriesRemarks() {
  _runRecheckGoodInquiriesRemarks(false);
}

/**
 * Cancel an in-progress resumable recheck (removes trigger + clears saved progress).
 */
function cancelRecheckGoodInquiriesRemarks() {
  _cleanupRecheckTrigger();
  _clearRecheckProgress();
  try {
    SpreadsheetApp.getUi().alert('Recheck cancelled. Progress cleared and resume trigger removed.');
  } catch (e) {}
}

/** Trigger handler — same logic but no UI alerts (no UI is attached when called by trigger). */
function _resumeRecheckGoodInquiriesRemarks() {
  _runRecheckGoodInquiriesRemarks(true);
}

function _runRecheckGoodInquiriesRemarks(fromTrigger) {
  const startMs = Date.now();
  const props = PropertiesService.getDocumentProperties();

  // Resolve target sheet — store ids on first run so the trigger can re-open it
  let sheet;
  if (fromTrigger) {
    const ssId = props.getProperty('rgir_ssId');
    const sheetId = props.getProperty('rgir_sheetId');
    if (!ssId || !sheetId) { _cleanupRecheckTrigger(); return; }
    const ss = SpreadsheetApp.openById(ssId);
    sheet = ss.getSheets().filter(function (s) { return String(s.getSheetId()) === sheetId; })[0];
    if (!sheet) { _cleanupRecheckTrigger(); _clearRecheckProgress(); return; }
  } else {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    sheet = ss.getActiveSheet();
    props.setProperties({
      'rgir_ssId': ss.getId(),
      'rgir_sheetId': String(sheet.getSheetId())
    }, false);
  }

  const lastRow = sheet.getLastRow();
  if (lastRow < 2) {
    if (!fromTrigger) {
      try { SpreadsheetApp.getUi().alert('No data rows.'); } catch (e) {}
    }
    _cleanupRecheckTrigger();
    _clearRecheckProgress();
    return;
  }

  const icCol = findColumnByHeader(sheet, 'IC');
  if (icCol < 0) {
    if (!fromTrigger) {
      try { SpreadsheetApp.getUi().alert('Column "IC" not found.'); } catch (e) {}
    }
    _cleanupRecheckTrigger();
    _clearRecheckProgress();
    return;
  }
  const remarksCol = findColumnByHeader(sheet, 'Remarks');
  if (remarksCol < 0) {
    if (!fromTrigger) {
      try { SpreadsheetApp.getUi().alert('Column "Remarks" not found.'); } catch (e) {}
    }
    _cleanupRecheckTrigger();
    _clearRecheckProgress();
    return;
  }

  // Batch read once per chunk (much faster than per-cell getValue inside the loop)
  const ics = sheet.getRange(2, icCol, lastRow - 1, 1).getValues();
  const remarks = sheet.getRange(2, remarksCol, lastRow - 1, 1).getValues();

  // Restore progress; on a fresh interactive run, this is 0/empty
  let startRow = parseInt(props.getProperty('rgir_lastRow') || '1', 10) + 1;
  if (startRow < 2) startRow = 2;
  let rechecked = parseInt(props.getProperty('rgir_rechecked') || '0', 10);
  let changedToBad = parseInt(props.getProperty('rgir_changed') || '0', 10);
  let changedRows;
  try { changedRows = JSON.parse(props.getProperty('rgir_changedRows') || '[]'); }
  catch (e) { changedRows = []; }

  let r;
  for (r = startRow; r <= lastRow; r++) {
    // Save progress and yield if we've used most of our time budget
    if (Date.now() - startMs > RGIR_MAX_RUN_MS) {
      props.setProperties({
        'rgir_lastRow': String(r - 1),
        'rgir_rechecked': String(rechecked),
        'rgir_changed': String(changedToBad),
        'rgir_changedRows': JSON.stringify(changedRows)
      }, false);
      _scheduleRecheckResume();
      try {
        SpreadsheetApp.getActive().toast(
          'Pausing at row ' + (r - 1) + '. Continuing automatically in ~1 min. Rechecked so far: ' + rechecked + ', Changed to BAD: ' + changedToBad,
          'Bad Debt Recheck',
          8
        );
      } catch (e) {}
      return;
    }

    const ic = ics[r - 2][0];
    const existing = remarks[r - 2][0];
    const icStr = ic !== null && ic !== undefined ? String(ic).trim() : '';
    const existingStr = existing !== null && existing !== undefined ? String(existing) : '';
    const existingLower = existingStr.toLowerCase();

    if (icStr === '' || existingLower.indexOf('good') === -1) continue;

    const newResult = checkBadDebtApi(icStr);
    rechecked++;

    if (newResult.toLowerCase().indexOf('bad') !== -1) {
      const cell = sheet.getRange(r, remarksCol);
      const oldPattern = /w:(true|false),\s*ri:(true|false),\s*good/gi;
      const updatedRemarks = existingStr.replace(oldPattern, newResult);
      cell.setValue(updatedRemarks);
      cell.setBackground('#fef08a');
      cell.setFontColor('#854d0e');
      cell.setFontWeight('bold');
      changedToBad++;
      changedRows.push(r);
    }

    Utilities.sleep(BAD_DEBT_DELAY_MS);
  }

  // Done — clean up trigger and saved progress
  _cleanupRecheckTrigger();
  _clearRecheckProgress();

  const message = 'Recheck complete!\n\n' +
    'Total rechecked: ' + rechecked + '\n' +
    'Changed to BAD: ' + changedToBad +
    (changedToBad > 0 ? '\n\nRows with changes (highlighted in yellow): ' + changedRows.join(', ') : '');

  if (!fromTrigger) {
    try { SpreadsheetApp.getUi().alert(message); } catch (e) {}
  } else {
    try {
      SpreadsheetApp.getActive().toast(
        'Recheck finished. Rechecked: ' + rechecked + ', Changed to BAD: ' + changedToBad,
        'Bad Debt Recheck',
        10
      );
    } catch (e) {}
    Logger.log(message);
  }
}

function _scheduleRecheckResume() {
  _cleanupRecheckTrigger();
  ScriptApp.newTrigger(RGIR_RESUME_HANDLER)
    .timeBased()
    .after(RGIR_RESUME_AFTER_MS)
    .create();
}

function _cleanupRecheckTrigger() {
  const triggers = ScriptApp.getProjectTriggers();
  for (let i = 0; i < triggers.length; i++) {
    if (triggers[i].getHandlerFunction() === RGIR_RESUME_HANDLER) {
      ScriptApp.deleteTrigger(triggers[i]);
    }
  }
}

function _clearRecheckProgress() {
  const props = PropertiesService.getDocumentProperties();
  for (let i = 0; i < RGIR_PROPS_KEYS.length; i++) {
    props.deleteProperty(RGIR_PROPS_KEYS[i]);
  }
}

/**
 * Add Bad Debt menu when spreadsheet opens (runs alongside other onOpen() in the project)
 */
function onOpen() {
  SpreadsheetApp.getUi()
    .createMenu('Bad Debt')
    .addItem('Check (IC Number → Bad Debt Check column)', 'checkBadDebtsForSheet')
    .addItem('Check → fill Remarks (IC column)', 'checkBadDebtsForRemarksSheet')
    .addItem('🛑 Cancel check → Remarks', 'cancelCheckBadDebtsForRemarksSheet')
    .addSeparator()
    .addItem('🔄 Recheck "Good" inquiries (IC Number sheet)', 'recheckGoodInquiries')
    .addItem('🔄 Recheck "Good" inquiries (Remarks sheet)', 'recheckGoodInquiriesRemarks')
    .addItem('🛑 Cancel recheck → Remarks', 'cancelRecheckGoodInquiriesRemarks')
    .addToUi();
}
