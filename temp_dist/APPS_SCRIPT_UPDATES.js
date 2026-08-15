// ============================================================
// TAMBAHKAN FUNGSI INI KE GOOGLE APPS SCRIPT YANG SUDAH ADA
// ============================================================

// TAMBAH DI BAGIAN doGet (sebelum return resJSON({ status: "error" }))

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

// ============================================================
// TAMBAH DI BAGIAN doPost (sebelum return resJSON({ status: "error" }))

    // --- UPDATE JADWAL EXISTING (Admin) ---
    if (data.action == "updateJadwal") {
      var sheet = ss.getSheetByName("JADWAL_KELAS");
      var sheetData = sheet.getDataRange().getDisplayValues(); // Gunakan getDisplayValues
      var updated = false;
      
      // Cari jadwal berdasarkan original tanggal, jam, dosen untuk identifikasi
      for (var i = 1; i < sheetData.length; i++) {
        var rowTanggal = (sheetData[i][0] || "").toString().trim();
        var rowJam = (sheetData[i][2] || "").toString().trim();
        var rowDosen = (sheetData[i][3] || "").toString().trim();

        var targetTanggal = (data.original_tanggal || "").toString().trim();
        var targetJam = (data.original_jam || "").toString().trim();
        var targetDosen = (data.original_dosen || "").toString().trim();

        var normRowJam = rowJam.replace(/WIB/gi, "").replace(/\./g, ":").trim().replace(/^0/, "");
        var normTargetJam = targetJam.replace(/WIB/gi, "").replace(/\./g, ":").trim().replace(/^0/, "");

        if (rowTanggal == targetTanggal &&
            normRowJam == normTargetJam &&
            rowDosen == targetDosen) {
          // Update row
          sheet.getRange(i + 1, 1).setValue(data.tanggal);
          sheet.getRange(i + 1, 2).setValue(data.materi);
          sheet.getRange(i + 1, 3).setValue(data.jam || "");
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
      var sheetData = sheet.getDataRange().getDisplayValues(); // Gunakan getDisplayValues
      
      for (var i = 1; i < sheetData.length; i++) {
        var rowTanggal = (sheetData[i][0] || "").toString().trim();
        var rowJam = (sheetData[i][2] || "").toString().trim();
        var rowDosen = (sheetData[i][3] || "").toString().trim();

        var targetTanggal = (data.tanggal || "").toString().trim();
        var targetJam = (data.jam || "").toString().trim();
        var targetDosen = (data.dosen || "").toString().trim();

        var normRowJam = rowJam.replace(/WIB/gi, "").replace(/\./g, ":").trim().replace(/^0/, "");
        var normTargetJam = targetJam.replace(/WIB/gi, "").replace(/\./g, ":").trim().replace(/^0/, "");

        if (rowTanggal == targetTanggal &&
            normRowJam == normTargetJam &&
            rowDosen == targetDosen) {
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

// ============================================================
// SUMMARY PERUBAHAN:
// 1. Tambahkan getMatakuliah di doGet - return list mapel untuk dropdown
// 2. Tambahkan updateJadwal di doPost - update jadwal existing
// 3. Tambahkan deleteJadwal di doPost - delete jadwal
// 4. Tambahkan updateMateriYoutube di doPost - admin update YT link
// 5. Tambahkan updateTugas di doPost - update tugas
// ============================================================
