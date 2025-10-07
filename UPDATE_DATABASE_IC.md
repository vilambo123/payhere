# 🔄 Update Database - Add IC Number Field

## Quick Update for Existing Database

If you already imported the database, you need to add the IC number column.

---

## 📝 Step-by-Step Instructions

### Method 1: Using phpMyAdmin (Easiest)

1. **Open phpMyAdmin:**
   ```
   http://localhost/phpmyadmin
   ```

2. **Select Database:**
   - Click on `loan_system` in the left sidebar

3. **Click on SQL Tab:**
   - Click the "SQL" tab at the top

4. **Copy and Paste This SQL:**
   ```sql
   ALTER TABLE `inquiries` 
   ADD COLUMN `ic_number` VARCHAR(14) DEFAULT NULL AFTER `phone`,
   ADD KEY `idx_ic_number` (`ic_number`);
   ```

5. **Click "Go" Button**
   - Should show: "1 row affected"
   - ✅ Done!

---

### Method 2: Import SQL File

1. **Open phpMyAdmin:**
   ```
   http://localhost/phpmyadmin
   ```

2. **Select `loan_system` Database**

3. **Click "Import" Tab**

4. **Choose File:**
   - Select `add_ic_column.sql`

5. **Click "Go"**
   - ✅ Done!

---

## ✅ Verify the Update

**Check if IC column was added:**

1. In phpMyAdmin, click on `inquiries` table
2. Click "Structure" tab
3. You should see `ic_number` field after `phone`

**Or run this SQL:**
```sql
DESCRIBE inquiries;
```

Should show:
```
...
phone        varchar(20)
ic_number    varchar(14)  ✅ NEW!
loan_type    varchar(50)
...
```

---

## 🎯 After Update

**Now the form will:**
- ✅ Require IC number input
- ✅ Validate Malaysian IC format (YYMMDD-PB-###G)
- ✅ Auto-format IC numbers (901231015678 → 901231-01-5678)
- ✅ Check for duplicate pending applications by IC
- ✅ Show polite message if application already pending

---

**Test the form after updating!**
```
http://localhost/payhere/
```

Fill in IC number like: `901231-01-5678`
