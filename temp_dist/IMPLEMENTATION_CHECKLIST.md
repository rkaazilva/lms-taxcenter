# 🎯 Implementation Checklist - Admin LMS & Fixes

## ✅ COMPLETED - Laravel Backend

### Files Modified Successfully:
- ✅ `app/Services/GoogleSheetService.php` - Added 4 new methods for admin features
- ✅ `app/Http/Controllers/AdminLmsController.php` - Updated jadwal update/delete logic
- ✅ `app/Http/Controllers/GuruController.php` - Added matakuliah fetch & pass to view
- ✅ `resources/views/guru/dashboard.blade.php` - Removed YouTube field, dynamic mapel dropdown
- ✅ `resources/views/admin-lms/jadwal/index.blade.php` - Fixed modal, dropdowns, delete form
- ✅ `routes/web.php` - Added materi update-youtube route
- ✅ All files: No syntax errors

---

## ⏳ ACTION REQUIRED - Google Apps Script Updates

**File Location**: Your Google Apps Script project
**Source**: `APPS_SCRIPT_UPDATES.js` (in project root)

### STEP 1: Add to `doGet()` function
Find the line: `return resJSON({ status: "error", message: "Action tidak dikenali" });`
ADD THIS BEFORE IT:

```javascript
  // --- AMBIL DAFTAR MATA PELAJARAN (untuk dropdown) ---
  if (action == "getMatakuliah") {
    var sheet = ss.getSheetByName("MATERI_BELAJAR") || ss.insertSheet("MATERI_BELAJAR");
    var data = sheet.getDataRange().getValues();
    var matakuliah = {};
    for (var i = 1; i < data.length; i++) {
      if (data[i][0] && !matakuliah[data[i][0]]) {
        matakuliah[data[i][0]] = true;
      }
    }
    var result = Object.keys(matakuliah).sort();
    return resJSON({ status: "success", data: result });
  }
```

### STEP 2: Add to `doPost()` function
Find the line: `return resJSON({ status: "error", message: "Action tidak dikenali" });`
ADD THIS BEFORE IT:

```javascript
    // --- UPDATE JADWAL EXISTING (Admin) ---
    if (data.action == "updateJadwal") {
      var sheet = ss.getSheetByName("JADWAL_KELAS");
      var sheetData = sheet.getDataRange().getValues();
      var updated = false;
      
      // Cari jadwal berdasarkan original tanggal, jam, dosen untuk identifikasi
      for (var i = 1; i < sheetData.length; i++) {
        if (sheetData[i][0] == data.original_tanggal && 
            sheetData[i][2] == data.original_jam && 
            sheetData[i][3] == data.original_dosen) {
          // Update row
          sheet.getRange(i + 1, 1).setValue(data.tanggal);
          sheet.getRange(i + 1, 2).setValue(data.materi);
          sheet.getRange(i + 1, 3).setValue(data.jam);
          sheet.getRange(i + 1, 4).setValue(data.dosen);
          sheet.getRange(i + 1, 5).setValue(data.moderator || "");
          sheet.getRange(i + 1, 6).setValue(data.link);
          updated = true;
          break;
        }
      }
      
      if (updated) {
        return resJSON({ status: "success", message: "Jadwal berhasil diperbarui!" });
      } else {
        return resJSON({ status: "error", message: "Jadwal tidak ditemukan!" });
      }
    }

    // --- DELETE JADWAL (Admin) ---
    if (data.action == "deleteJadwal") {
      var sheet = ss.getSheetByName("JADWAL_KELAS");
      var sheetData = sheet.getDataRange().getValues();
      
      for (var i = 1; i < sheetData.length; i++) {
        if (sheetData[i][0] == data.tanggal && 
            sheetData[i][2] == data.jam && 
            sheetData[i][3] == data.dosen) {
          sheet.deleteRow(i + 1);
          return resJSON({ status: "success", message: "Jadwal berhasil dihapus!" });
        }
      }
      return resJSON({ status: "error", message: "Jadwal tidak ditemukan!" });
    }

    // --- UPDATE YOUTUBE LINK MATERI (Admin) ---
    if (data.action == "updateMateriYoutube") {
      var sheet = ss.getSheetByName("MATERI_BELAJAR");
      var sheetData = sheet.getDataRange().getValues();
      var updated = false;
      
      // Cari materi berdasarkan mapel dan judul
      for (var i = 1; i < sheetData.length; i++) {
        if (sheetData[i][0] == data.mapel && sheetData[i][1] == data.judul) {
          sheet.getRange(i + 1, 4).setValue(data.link_youtube); // Column D (index 3)
          updated = true;
          break;
        }
      }
      
      if (updated) {
        return resJSON({ status: "success", message: "Link YouTube berhasil diperbarui!" });
      } else {
        return resJSON({ status: "error", message: "Materi tidak ditemukan!" });
      }
    }

    // --- UPDATE TUGAS (Admin) ---
    if (data.action == "updateTugas") {
      var sheet = ss.getSheetByName("TUGAS_KELAS");
      var sheetData = sheet.getDataRange().getValues();
      var updated = false;
      
      for (var i = 1; i < sheetData.length; i++) {
        if (sheetData[i][0] == data.id_tugas) {
          sheet.getRange(i + 1, 1).setValue(data.id_tugas);
          sheet.getRange(i + 1, 2).setValue(data.mapel);
          sheet.getRange(i + 1, 3).setValue(data.judul);
          sheet.getRange(i + 1, 4).setValue(data.deskripsi);
          sheet.getRange(i + 1, 5).setValue(data.link_soal);
          sheet.getRange(i + 1, 6).setValue(data.deadline);
          updated = true;
          break;
        }
      }
             
      if (updated) {
        return resJSON({ status: "success", message: "Tugas berhasil diperbarui!" });
      } else {
        return resJSON({ status: "error", message: "Tugas tidak ditemukan!" });
      }
    }
```

---

## 📋 Testing Checklist

After pasting the Apps Script code, test these in browser:

### Admin Panel Tests:
- [ ] Login as ADMIN_LMS → should redirect to `/admin-lms`
- [ ] View jadwal list → should show newest first
- [ ] Click "Tambah Jadwal" → modal should open
- [ ] Add new jadwal with mapel dropdown → should save to sheet
- [ ] Click "Edit" on existing jadwal → modal should pre-fill all fields
- [ ] Update jadwal → should update in sheet
- [ ] Click "Hapus" → should delete from sheet
- [ ] View materi list → should show all materi
- [ ] Check YouTube link can be edited (button or admin panel)

### Guru Panel Tests:
- [ ] Login as GURU/TUTOR → should show guru dashboard
- [ ] Add new materi → NO YouTube field visible (should only have PDF + keterangan)
- [ ] Add new tugas → mapel dropdown should auto-populate from sheet
- [ ] Check materi list → YouTube links should still display (admin-set)

### General Tests:
- [ ] No console errors
- [ ] No validation errors
- [ ] Time formats show 24-hour (13:30 not 1:30 PM)
- [ ] File uploads still work (30MB limit)

---

## 🔍 What Each Fix Does

| # | Issue | Solution | Status |
|---|-------|----------|--------|
| 1 | Jadwal shows oldest first | Added `usort()` descending in controller | ✅ |
| 2 | Guru can edit YouTube links | Removed field from guru form, admin-only panel | ✅ |
| 3 | Tugas mapel hardcoded | Dynamic dropdown from getMatakuliah() | ✅ |
| 4 | File size 10MB | Updated to 30MB (in frontend/submission) | ✅ |
| 5 | Admin time format confusing | Changed to 24-hour WIB format | ✅ |
| 6 | Admin jadwal edit not working | Fixed original field identification, modal logic | ✅ |
| 7 | Absensi view/edit | Still using Google Sheet (not changed) | 🔲 |

---

## 📞 Need Help?

If something doesn't work:
1. Check browser console for errors (F12 → Console)
2. Check Laravel logs: `storage/logs/laravel.log`
3. Test Apps Script with `curl` or browser

---

**Ready to test? Follow the Apps Script steps above first! 🚀**
