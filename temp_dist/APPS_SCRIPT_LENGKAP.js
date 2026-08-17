// ============================================================
// KONFIGURASI UTAMA & KEAMANAN
// ============================================================
var SECURE_TOKEN = "TC_UIN_LMS_SECURE_2026"; // Token rahasia Anda, samakan di .env Laravel
var LARAVEL_APP_URL = "https://taxcenteruinbandung.com"; // Domain website LMS Anda
var DRIVE_FOLDER_ID = "1DffOX_twduIrlgrzCu003kvWJhe_WapF"; // Folder tempat simpan Tugas
var DRIVE_MATERI_FOLDER_ID = "1plvX7fT4pyVA8_sz5X2Sdtc_GPX6cznl"; // Folder 06_MATERI_AJAR di Drive Anda


// ============================================================
// 1. GATEWAY GET (Membaca Data dengan Pengaman Token)
// ============================================================
function doGet(e) {
  var action = e.parameter.action;
  var token = e.parameter.token;

  // Proteksi Keamanan API
  if (token !== SECURE_TOKEN) {
    return resJSON({ status: "error", message: "Akses Ditolak! Token Keamanan Tidak Valid." });
  }

  var ss = SpreadsheetApp.getActiveSpreadsheet();

  // --- LOGIN ---
  if (action == "login") {
    var emailInput = e.parameter.email;
    var passInput = e.parameter.pass;
    var sheet = ss.getSheetByName("DATA_LOGIN");
    var data = sheet.getDataRange().getValues();
    var result = { status: "failed", message: "Email/Password Salah!", url: "" };

    for (var i = 1; i < data.length; i++) {
      if (data[i][0] == emailInput && data[i][1] == passInput) {
        result.status = "success";
        result.message = "Halo " + data[i][3];
        result.url = data[i][4];
        result.role = data[i][2];
        result.sertifikat = data[i][5] || ""; // Kolom F (index 5)
        result.kelas = data[i][6] || ""; // Kolom G (index 6) - BATCH/KELAS
        break;
      }
    }
    return resJSON(result);
  }

  // --- AMBIL JADWAL (Menggunakan getDisplayValues agar data tanggal/waktu dibaca sebagai string visual seragam) ---
  if (action == "getJadwal") {
    var sheet = ss.getSheetByName("JADWAL_KELAS");
    var data = sheet.getDataRange().getDisplayValues(); // Gunakan getDisplayValues
    var result = [];
    for (var i = 1; i < data.length; i++) {
      if (!data[i][1]) continue;
      result.push({
        "tanggal": data[i][0],
        "materi": data[i][1],
        "jam": data[i][2],
        "dosen": data[i][3],
        "link": data[i][5] // Column F (index 5)
      });
    }
    return resJSON(result);
  }

  // --- AMBIL SEMUA MATERI & REKAMAN ---
  if (action == "getMateri") {
    var sheet = ss.getSheetByName("MATERI_BELAJAR") || ss.insertSheet("MATERI_BELAJAR");
    var data = sheet.getDataRange().getValues();
    var result = [];
    for (var i = 1; i < data.length; i++) {
      if (!data[i][0]) continue;
      result.push({
        "mapel": data[i][0],
        "judul": data[i][1],
        "link_modul": data[i][2],
        "link_youtube": data[i][3],
        "keterangan": data[i][4],
        "status": data[i][5] || "Rilis",
        "kelas": data[i][6] || "" // Kolom G (index 6) - Target Kelas
      });
    }
    return resJSON(result);
  }

  // --- AMBIL DAFTAR DEFINISI TUGAS ---
  if (action == "getTugas") {
    var sheet = ss.getSheetByName("TUGAS_KELAS") || ss.insertSheet("TUGAS_KELAS");
    var data = sheet.getDataRange().getValues();
    var result = [];
    for (var i = 1; i < data.length; i++) {
      if (!data[i][0]) continue;
      result.push({
        "id_tugas": data[i][0],
        "mapel": data[i][1],
        "judul": data[i][2],
        "deskripsi": data[i][3],
        "link_soal": data[i][4],
        "deadline": data[i][5]
      });
    }
    return resJSON(result);
  }

  // --- AMBIL REKAP NILAI & TUGAS SISWA ---
  if (action == "getNilaiSiswa") {
    var email = e.parameter.email;
    var sheet = ss.getSheetByName("SUBMISSION_TUGAS") || ss.insertSheet("SUBMISSION_TUGAS");
    var data = sheet.getDataRange().getValues();
    var result = [];
    for (var i = 1; i < data.length; i++) {
      if (data[i][1] == email) {
        result.push({
          "id_tugas": data[i][3],
          "link_tugas": data[i][4],
          "nilai": data[i][5] || "-",
          "feedback": data[i][6] || "-"
        });
      }
    }
    return resJSON(result);
  }

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

  // --- AMBIL DAFTAR SEMUA SISWA (untuk rekap kelas) ---
  if (action == "getAllSiswa") {
    var sheet = ss.getSheetByName("DATA_LOGIN") || ss.insertSheet("DATA_LOGIN");
    var data = sheet.getDataRange().getValues();
    var result = [];
    for (var i = 1; i < data.length; i++) {
      if (data[i][2] && (data[i][2].toString().toUpperCase() === "SISWA" || data[i][2].toString().toUpperCase() === "PESERTA")) {
        result.push({
          "email": data[i][0],
          "nama": data[i][3] || "Peserta"
        });
      }
    }
    return resJSON(result);
  }

  // --- AMBIL SEMUA SUBMISSION TUGAS (untuk Penilaian Guru, digabung dengan info mapel dari TUGAS_KELAS) ---
  if (action == "getAllSubmissions") {
    var sheetSub = ss.getSheetByName("SUBMISSION_TUGAS") || ss.insertSheet("SUBMISSION_TUGAS");
    var sheetTug = ss.getSheetByName("TUGAS_KELAS") || ss.insertSheet("TUGAS_KELAS");
    var subData = sheetSub.getDataRange().getValues();
    var tugData = sheetTug.getDataRange().getValues();

    // Buat map id_tugas -> { mapel, judul }
    var tugasMap = {};
    for (var i = 1; i < tugData.length; i++) {
      if (tugData[i][0]) {
        tugasMap[tugData[i][0]] = { mapel: tugData[i][1], judul: tugData[i][2] };
      }
    }

    var result = [];
    for (var i = 1; i < subData.length; i++) {
      if (!subData[i][1]) continue; // skip baris kosong
      var idTugas = subData[i][3];
      var tugInfo = tugasMap[idTugas] || { mapel: '-', judul: '-' };
      result.push({
        "row": i + 1,
        "timestamp": subData[i][0].toString(),
        "email": subData[i][1],
        "nama": subData[i][2],
        "id_tugas": idTugas,
        "mapel": tugInfo.mapel,
        "judul_tugas": tugInfo.judul,
        "link_file": subData[i][4],
        "nilai": subData[i][5] !== undefined && subData[i][5] !== "" ? subData[i][5] : "-",
        "feedback": subData[i][6] !== undefined && subData[i][6] !== "" ? subData[i][6] : "-"
      });
    }
    return resJSON(result);
  }

  return resJSON({ status: "error", message: "Action tidak dikenali" });
}

// ============================================================
// 2. GATEWAY POST (Menulis Data / Upload dengan Pengaman Token)
// ============================================================
function doPost(e) {
  try {
    var data = JSON.parse(e.postData.contents);

    // Proteksi Keamanan API
    if (data.token !== SECURE_TOKEN) {
      return resJSON({ status: "error", message: "Akses Ditolak! Token Keamanan Tidak Valid." });
    }

    var ss = SpreadsheetApp.getActiveSpreadsheet();

    // --- ABSENSI OTOMATIS / MANUAL REKAMAN ---
    if (data.action == "catatAbsen") {
      var sheetAbsen = ss.getSheetByName("ABSENSI_PESERTA") || ss.insertSheet("ABSENSI_PESERTA");
      if (sheetAbsen.getLastRow() == 0) {
        sheetAbsen.appendRow(["Timestamp", "Email Siswa", "Nama Siswa", "Mata Pelajaran", "Status", "Metode"]);
      }
      sheetAbsen.appendRow([
        new Date(),
        data.email,
        data.nama,
        data.mapel,
        "HADIR",
        data.metode // "Live Zoom" atau "Nonton Rekaman YouTube"
      ]);
      return resJSON({ status: "success", message: "Kehadiran berhasil dicatat!" });
    }

    // --- AMBIL SEMUA ABSENSI (Admin/Guru) ---
    if (data.action == "getAllAbsensi") {
      var sheet = ss.getSheetByName("ABSENSI_PESERTA");
      if (!sheet) return resJSON([]);
      var sheetData = sheet.getDataRange().getValues();
      var list = [];
      for (var i = 1; i < sheetData.length; i++) {
        list.push({
          timestamp: sheetData[i][0],
          email: sheetData[i][1],
          nama: sheetData[i][2],
          mapel: sheetData[i][3],
          status: sheetData[i][4],
          metode: sheetData[i][5]
        });
      }
      return resJSON(list);
    }

    // --- AMBIL ABSENSI SISWA SPESIFIK (Siswa) ---
    if (data.action == "getAbsensiSiswa") {
      var sheet = ss.getSheetByName("ABSENSI_PESERTA");
      if (!sheet) return resJSON([]);
      var sheetData = sheet.getDataRange().getValues();
      var list = [];
      var targetEmail = (data.email || "").toString().toLowerCase().trim();
      for (var i = 1; i < sheetData.length; i++) {
        var emailRow = (sheetData[i][1] || "").toString().toLowerCase().trim();
        if (emailRow == targetEmail) {
          list.push({
            timestamp: sheetData[i][0],
            email: sheetData[i][1],
            nama: sheetData[i][2],
            mapel: sheetData[i][3],
            status: sheetData[i][4],
            metode: sheetData[i][5]
          });
        }
      }
      return resJSON(list);
    }

    // --- TAMBAH ABSENSI MANUAL (Admin/Guru) ---
    if (data.action == "addAbsensiManual") {
      var sheet = ss.getSheetByName("ABSENSI_PESERTA") || ss.insertSheet("ABSENSI_PESERTA");
      if (sheet.getLastRow() == 0) {
        sheet.appendRow(["Timestamp", "Email Siswa", "Nama Siswa", "Mata Pelajaran", "Status", "Metode"]);
      }
      var dateVal = data.timestamp ? new Date(data.timestamp) : new Date();
      sheet.appendRow([
        dateVal,
        data.email,
        data.nama,
        data.mapel,
        data.status || "HADIR",
        data.metode || "Manual (Admin)"
      ]);
      clearLaravelCache();
      return resJSON({ status: "success", message: "Absensi berhasil ditambahkan!" });
    }

    // --- HAPUS ABSENSI (Admin) ---
    if (data.action == "deleteAbsensi") {
      var sheet = ss.getSheetByName("ABSENSI_PESERTA");
      if (!sheet) return resJSON({ status: "error", message: "Sheet tidak ditemukan!" });
      var sheetData = sheet.getDataRange().getValues();
      var targetEmail = (data.email || "").toString().toLowerCase().trim();
      var targetMapel = (data.mapel || "").toString().trim();
      var targetTimestamp = new Date(data.timestamp);
      
      var deleted = false;
      for (var i = sheetData.length - 1; i >= 1; i--) {
        var rowEmail = (sheetData[i][1] || "").toString().toLowerCase().trim();
        var rowMapel = (sheetData[i][3] || "").toString().trim();
        var rowTimestamp = new Date(sheetData[i][0]);
        
        if (rowEmail == targetEmail && rowMapel == targetMapel && Math.abs(rowTimestamp.getTime() - targetTimestamp.getTime()) < 5000) {
          sheet.deleteRow(i + 1);
          deleted = true;
          break;
        }
      }
      if (deleted) {
        clearLaravelCache();
        return resJSON({ status: "success", message: "Absensi berhasil dihapus!" });
      }
      return resJSON({ status: "error", message: "Data absensi tidak ditemukan!" });
    }

    // --- UPLOAD TUGAS (Dinamis Per Tugas) ---
    if (data.action == "submitTugas") {
      var submissionLink = "";

      // Jika ada file yang diunggah
      if (data.base64 && data.fileName && data.mimeType) {
        var folder = DriveApp.getFolderById(DRIVE_FOLDER_ID);
        var contentType = data.mimeType;
        var bytes = Utilities.base64Decode(data.base64);
        var blob = Utilities.newBlob(bytes, contentType, data.fileName);
        var file = folder.createFile(blob);
        // Set akses publik agar bisa dibaca guru/dosen
        file.setSharing(DriveApp.Access.ANYONE_WITH_LINK, DriveApp.Permission.VIEW);
        submissionLink = file.getUrl();
      } else if (data.link_tugas) {
        // Jika hanya mengirim link tugas
        submissionLink = data.link_tugas;
      } else {
        return resJSON({ status: "error", message: "Harap unggah berkas atau masukkan link tugas!" });
      }

      // Catat ke Sheet "SUBMISSION_TUGAS"
      var sheetSub = ss.getSheetByName("SUBMISSION_TUGAS") || ss.insertSheet("SUBMISSION_TUGAS");
      if (sheetSub.getLastRow() == 0) {
        sheetSub.appendRow(["Timestamp", "Email Siswa", "Nama Siswa", "ID Tugas", "Link File Tugas", "Nilai", "Feedback"]);
      }

      sheetSub.appendRow([
        new Date(),
        data.email,
        data.nama,
        data.id_tugas,
        submissionLink,
        "", // Nilai (Diisi Manual oleh Dosen di Sheet)
        ""  // Feedback (Diisi Manual oleh Dosen di Sheet)
      ]);

      clearLaravelCache();
      return resJSON({ status: "success", message: "Tugas Terkirim!", link: submissionLink });
    }

    // --- TAMBAH MATERI BARU (Tutor) ---
    if (data.action == "addMateri") {
      var sheetMat = ss.getSheetByName("MATERI_BELAJAR") || ss.insertSheet("MATERI_BELAJAR");
      if (sheetMat.getLastRow() == 0) {
        sheetMat.appendRow(["Mata Pelajaran", "Judul Materi", "Link Modul", "Link Video Rekaman", "Keterangan", "Status", "Target Kelas"]);
      }
      sheetMat.appendRow([
        data.mapel,
        data.judul,
        data.link_modul,
        data.link_youtube,
        data.keterangan,
        data.status || "Rilis",
        data.kelas || ""
      ]);
      clearLaravelCache();
      return resJSON({ status: "success", message: "Materi berhasil ditambahkan!" });
    }

    // --- TAMBAH TUGAS BARU (Tutor) ---
    if (data.action == "addTugas") {
      var sheetTug = ss.getSheetByName("TUGAS_KELAS") || ss.insertSheet("TUGAS_KELAS");
      if (sheetTug.getLastRow() == 0) {
        sheetTug.appendRow(["ID Tugas", "Mata Pelajaran", "Judul Tugas", "Deskripsi Soal", "Link Soal", "Deadline"]);
      }
      sheetTug.appendRow([
        data.id_tugas,
        data.mapel,
        data.judul,
        data.deskripsi,
        data.link_soal,
        data.deadline
      ]);
      clearLaravelCache();

      var isBlasting = (data.blast === true || data.blast === "true" || data.blast === 1 || data.blast === "1");
      var blastMsg = "";
      if (isBlasting) {
        blastMsg = blastTugasOtomatis(data.id_tugas, data.mapel, data.judul, data.deskripsi, data.link_soal, data.deadline);

        // Kirim WA blast otomatis
        var waMsg = "📢 *TUGAS BARU DIRILIS*\n\n" +
          "Mata Pelajaran: *" + data.mapel + "*\n" +
          "Judul Tugas: *" + data.judul + "*\n" +
          "Deadline: " + data.deadline + "\n\n" +
          "Silakan cek deskripsi lengkap dan kumpulkan berkas Anda melalui LMS!";
        sendWaFonnte(waMsg);
      }

      var successMessage = "Tugas berhasil dibuat!";
      if (blastMsg.indexOf("TERKIRIM") !== -1) {
        successMessage += " Notifikasi email baru telah dikirim ke siswa otomatis.";
      } else if (blastMsg.indexOf("Gagal") !== -1) {
        successMessage += " Namun gagal kirim email: " + blastMsg;
      }

      return resJSON({ status: "success", message: successMessage });
    }

    // --- TAMBAH JADWAL BARU (Admin) ---
    if (data.action == "addJadwal") {
      var sheet = ss.getSheetByName("JADWAL_KELAS") || ss.insertSheet("JADWAL_KELAS");
      if (sheet.getLastRow() == 0) {
        sheet.appendRow(["Tanggal", "Mata Pelajaran/Materi", "Jam", "Dosen", "Moderator", "Link", "Aksi"]);
      }

      var tanggalStr = data.tanggal;
      var jamStr = data.jam || "";
      var matkulStr = data.materi || data.mapel;
      var dosenStr = data.dosen;
      var linkZoomStr = data.link;
      var statusBlast = "";

      // Jika parameter blast dikirim sebagai true, langsung kirim email blast
      if (data.blast === true || data.blast === "true" || data.blast === 1 || data.blast === "1") {
        var sheetPeserta = ss.getSheetByName("DATABASE_PESERTA");
        var dataPeserta = sheetPeserta.getDataRange().getValues();
        var emailList = [];
        for (var i = 1; i < dataPeserta.length; i++) {
          if (dataPeserta[i][2] != "" && dataPeserta[i][7] == "LUNAS") {
            emailList.push(dataPeserta[i][2]);
          }
        }
        if (emailList.length > 0) {
          blastJadwalEmail(matkulStr, tanggalStr, jamStr, linkZoomStr, emailList);
          statusBlast = "✅ TERKIRIM KE " + emailList.length + " SISWA";

          // Kirim WA blast otomatis
          var waMsg = "📢 *INFO JADWAL BARU*\n\n" +
            "Mata Pelajaran: *" + matkulStr + "*\n" +
            "Hari/Tanggal: " + tanggalStr + "\n" +
            "Jam: " + jamStr + " WIB\n" +
            "Dosen: " + dosenStr + "\n" +
            "Link Zoom: " + linkZoomStr + "\n\n" +
            "Silakan ikuti sesi kelas tepat waktu melalui LMS!";
          sendWaFonnte(waMsg);
        } else {
          statusBlast = "⚠️ Gagal: Tidak ada siswa berstatus LUNAS";
        }
      } else {
        statusBlast = "❌ TIDAK DIKIRIM";
      }

      sheet.appendRow([
        tanggalStr,
        matkulStr,
        jamStr,
        dosenStr,
        data.moderator || "",
        linkZoomStr,
        statusBlast
      ]);
      clearLaravelCache();

      var successMessage = "Jadwal berhasil ditambahkan!";
      if (statusBlast.indexOf("TERKIRIM") !== -1) {
        successMessage += " Notifikasi email juga telah dikirim otomatis.";
      } else if (statusBlast.indexOf("Gagal") !== -1) {
        successMessage += " Namun gagal kirim email karena tidak ada siswa LUNAS.";
      }

      return resJSON({ status: "success", message: successMessage });
    }

    // --- UPDATE JADWAL EXISTING (Admin - menggunakan getDisplayValues agar pencarian string tanggal cocok) ---
    if (data.action == "updateJadwal") {
      var sheet = ss.getSheetByName("JADWAL_KELAS");
      var sheetData = sheet.getDataRange().getDisplayValues(); // Gunakan getDisplayValues
      var updated = false;
      var statusBlast = "";
      var isBlasting = (data.blast === true || data.blast === "true" || data.blast === 1 || data.blast === "1");

      if (isBlasting) {
        var matkulStr = data.materi || data.mapel;
        var tanggalStr = data.tanggal;
        var jamStr = data.jam || "";
        var linkZoomStr = data.link;

        var sheetPeserta = ss.getSheetByName("DATABASE_PESERTA");
        var dataPeserta = sheetPeserta.getDataRange().getValues();
        var emailList = [];
        for (var i = 1; i < dataPeserta.length; i++) {
          if (dataPeserta[i][2] != "" && dataPeserta[i][7] == "LUNAS") {
            emailList.push(dataPeserta[i][2]);
          }
        }
        if (emailList.length > 0) {
          blastJadwalEmail(matkulStr, tanggalStr, jamStr, linkZoomStr, emailList);
          statusBlast = "✅ TERKIRIM KE " + emailList.length + " SISWA";

          // Kirim WA blast otomatis
          var waMsg = "📢 *INFO JADWAL BARU*\n\n" +
            "Mata Pelajaran: *" + matkulStr + "*\n" +
            "Hari/Tanggal: " + tanggalStr + "\n" +
            "Jam: " + jamStr + " WIB\n" +
            "Dosen: " + (data.dosen || "-") + "\n" +
            "Link Zoom: " + linkZoomStr + "\n\n" +
            "Silakan ikuti sesi kelas tepat waktu melalui LMS!";
          sendWaFonnte(waMsg);
        } else {
          statusBlast = "⚠️ Gagal: Tidak ada siswa berstatus LUNAS";
        }
      }

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
          if (isBlasting) {
            sheet.getRange(i + 1, 7).setValue(statusBlast);
          }
          updated = true;
          break;
        }
      }

      if (updated) {
        clearLaravelCache();
        var successMessage = "Jadwal berhasil diperbarui!";
        if (isBlasting) {
          if (statusBlast.indexOf("TERKIRIM") !== -1) {
            successMessage += " Notifikasi email juga telah dikirim otomatis.";
          } else if (statusBlast.indexOf("Gagal") !== -1) {
            successMessage += " Namun gagal kirim email karena tidak ada siswa LUNAS.";
          }
        }
        return resJSON({ status: "success", message: successMessage });
      } else {
        return resJSON({ status: "error", message: "Jadwal tidak ditemukan!" });
      }
    }

    // --- DELETE JADWAL (Admin - menggunakan getDisplayValues agar pencarian string tanggal cocok) ---
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
          clearLaravelCache();
          return resJSON({ status: "success", message: "Jadwal berhasil dihapus!" });
        }
      }
      return resJSON({ status: "error", message: "Jadwal tidak ditemukan!" });
    }

    // --- UPDATE PROFIL & PASSWORD SISWA/GURU (Self Service) ---
    if (data.action == "updateProfile") {
      var email = (data.email || "").toString().toLowerCase().trim();
      var sheet = ss.getSheetByName("DATA_LOGIN");
      var sheetData = sheet.getDataRange().getValues();
      var updated = false;

      for (var i = 1; i < sheetData.length; i++) {
        var rowEmail = (sheetData[i][0] || "").toString().toLowerCase().trim();
        if (rowEmail == email) {
          // Update Password jika dikirim dan tidak kosong
          if (data.new_password && data.new_password.toString().trim() !== "") {
            sheet.getRange(i + 1, 2).setValue(data.new_password);
          }
          // Update Nama jika dikirim dan tidak kosong
          if (data.nama && data.nama.toString().trim() !== "") {
            sheet.getRange(i + 1, 4).setValue(data.nama);
          }
          updated = true;
          break;
        }
      }

      if (updated) {
        clearLaravelCache();
        return resJSON({ status: "success", message: "Profil berhasil diperbarui!" });
      } else {
        return resJSON({ status: "error", message: "Akun login Anda tidak ditemukan di database Google Sheets!" });
      }
    }

    // --- UPDATE YOUTUBE LINK MATERI (Admin) ---
    if (data.action == "updateMateriYoutube") {
      var sheet = ss.getSheetByName("MATERI_BELAJAR");
      var sheetData = sheet.getDataRange().getValues();
      var updated = false;

      for (var i = 1; i < sheetData.length; i++) {
        if (sheetData[i][0] == data.mapel && sheetData[i][1] == data.judul) {
          sheet.getRange(i + 1, 4).setValue(data.link_youtube);
          updated = true;
          break;
        }
      }

      if (updated) {
        clearLaravelCache();
        return resJSON({ status: "success", message: "Link YouTube berhasil diperbarui!" });
      } else {
        return resJSON({ status: "error", message: "Materi tidak ditemukan!" });
      }
    }

    // --- UPDATE TUGAS (Admin/Guru) ---
    if (data.action == "updateTugas") {
      var sheet = ss.getSheetByName("TUGAS_KELAS");
      var sheetData = sheet.getDataRange().getValues();
      var updated = false;

      for (var i = 1; i < sheetData.length; i++) {
        if (sheetData[i][0] == data.original_id_tugas) {
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
        clearLaravelCache();

        var isBlasting = (data.blast === true || data.blast === "true" || data.blast === 1 || data.blast === "1");
        var blastMsg = "";
        if (isBlasting) {
          blastMsg = blastTugasOtomatis(data.id_tugas, data.mapel, data.judul, data.deskripsi, data.link_soal, data.deadline);

          // Kirim WA blast otomatis
          var waMsg = "📢 *TUGAS DIUPDATE*\n\n" +
            "Mata Pelajaran: *" + data.mapel + "*\n" +
            "Judul Tugas: *" + data.judul + "*\n" +
            "Deadline Baru: " + data.deadline + "\n\n" +
            "Silakan cek perubahan instruksi dan kumpulkan berkas Anda melalui LMS!";
          sendWaFonnte(waMsg);
        }

        var successMessage = "Tugas berhasil diperbarui!";
        if (blastMsg.indexOf("TERKIRIM") !== -1) {
          successMessage += " Notifikasi email perubahan tugas telah dikirim otomatis.";
        } else if (blastMsg.indexOf("Gagal") !== -1) {
          successMessage += " Namun gagal kirim email: " + blastMsg;
        }

        return resJSON({ status: "success", message: successMessage });
      } else {
        return resJSON({ status: "error", message: "Tugas tidak ditemukan!" });
      }
    }

    // --- UPDATE MATERI OLEH GURU (menggunakan original_mapel + original_judul sebagai kunci) ---
    if (data.action == "updateMateriByKey") {
      var sheet = ss.getSheetByName("MATERI_BELAJAR");
      var sheetData = sheet.getDataRange().getValues();
      var updated = false;

      for (var i = 1; i < sheetData.length; i++) {
        if (sheetData[i][0] == data.original_mapel && sheetData[i][1] == data.original_judul) {
          sheet.getRange(i + 1, 1).setValue(data.mapel);
          sheet.getRange(i + 1, 2).setValue(data.judul);
          sheet.getRange(i + 1, 3).setValue(data.link_modul);
          // Kolom 4 (link_youtube) TIDAK diupdate di sini — dikelola oleh Admin LMS
          sheet.getRange(i + 1, 5).setValue(data.keterangan);
          sheet.getRange(i + 1, 6).setValue(data.status || "Rilis");
          sheet.getRange(i + 1, 7).setValue(data.kelas || "");
          updated = true;
          break;
        }
      }

      if (updated) {
        clearLaravelCache();
        return resJSON({ status: "success", message: "Materi berhasil diperbarui!" });
      } else {
        return resJSON({ status: "error", message: "Materi tidak ditemukan!" });
      }
    }

    // --- UPLOAD FILE MATERI KE GOOGLE DRIVE (Guru) ---
    if (data.action == "submitMateriFile") {
      try {
        var folder = DriveApp.getFolderById(DRIVE_MATERI_FOLDER_ID);
        var bytes = Utilities.base64Decode(data.base64);
        var blob = Utilities.newBlob(bytes, data.mimeType, data.fileName);
        var file = folder.createFile(blob);
        // Set akses publik agar bisa dibuka siswa
        file.setSharing(DriveApp.Access.ANYONE_WITH_LINK, DriveApp.Permission.VIEW);
        return resJSON({ status: "success", message: "File berhasil diunggah!", link: file.getUrl() });
      } catch (uploadErr) {
        return resJSON({ status: "error", message: "Gagal upload: " + uploadErr.toString() });
      }
    }

    // --- BERI NILAI & FEEDBACK TUGAS SISWA (Guru) ---
    if (data.action == "penilaianTugas") {
      var sheet = ss.getSheetByName("SUBMISSION_TUGAS");
      if (!sheet) return resJSON({ status: "error", message: "Sheet SUBMISSION_TUGAS tidak ditemukan!" });
      var sheetData = sheet.getDataRange().getValues();
      var updated = false;

      for (var i = 1; i < sheetData.length; i++) {
        if (sheetData[i][1] == data.email && sheetData[i][3] == data.id_tugas) {
          sheet.getRange(i + 1, 6).setValue(data.nilai);    // Kolom F: Nilai
          sheet.getRange(i + 1, 7).setValue(data.feedback); // Kolom G: Feedback
          updated = true;
          break;
        }
      }

      if (updated) {
        clearLaravelCache();
        sendNotificationEmail(data.email, data.id_tugas, data.nilai, data.feedback);
        return resJSON({ status: "success", message: "Nilai berhasil disimpan!" });
      } else {
        return resJSON({ status: "error", message: "Data pengumpulan tidak ditemukan!" });
      }
    }

    // --- BATCH BERI NILAI & FEEDBACK TUGAS SISWA (Guru) ---
    if (data.action == "batchPenilaianTugas") {
      var sheet = ss.getSheetByName("SUBMISSION_TUGAS");
      if (!sheet) return resJSON({ status: "error", message: "Sheet SUBMISSION_TUGAS tidak ditemukan!" });

      var range = sheet.getDataRange();
      var sheetData = range.getValues();
      var items = data.items || [];
      var updateCount = 0;

      for (var k = 0; k < items.length; k++) {
        var item = items[k];
        for (var i = 1; i < sheetData.length; i++) {
          if (sheetData[i][1] == item.email && sheetData[i][3] == item.id_tugas) {
            sheetData[i][5] = item.nilai;    // Kolom F: Nilai
            sheetData[i][6] = item.feedback; // Kolom G: Feedback
            updateCount++;
            break;
          }
        }
      }

      if (updateCount > 0) {
        range.setValues(sheetData);
        clearLaravelCache();

        // Kirim email notifikasi secara massal
        for (var k = 0; k < items.length; k++) {
          try {
            sendNotificationEmail(items[k].email, items[k].id_tugas, items[k].nilai, items[k].feedback);
          } catch (eEmail) {
            console.log("Gagal kirim email massal untuk " + items[k].email + ": " + eEmail.toString());
          }
        }

        return resJSON({ status: "success", message: "Berhasil memperbarui " + updateCount + " nilai!" });
      } else {
        return resJSON({ status: "error", message: "Tidak ada data pengumpulan yang cocok untuk diperbarui." });
      }
    }

    return resJSON({ status: "error", message: "Action tidak dikenali" });

  } catch (err) {
    return resJSON({ status: "error", message: err.toString() });
  }
}

// ============================================================
// 3. ROBOT EMAIL + AUTO-REGISTER (TETAP INTAK / GAK DIBUANG)
// ============================================================
function kirimEmailOtomatis(e) {
  var sheet = e.source.getActiveSheet();
  var range = e.range;

  if (sheet.getName() == "DATABASE_PESERTA" && range.getColumn() == 8 && e.value == "LUNAS") {
    var row = range.getRow();
    var nama = sheet.getRange(row, 2).getValue();
    var email = sheet.getRange(row, 3).getValue();
    var passDefault = Math.floor(100000 + Math.random() * 900000).toString();

    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheetLogin = ss.getSheetByName("DATA_LOGIN");
    var dataLogin = sheetLogin.getDataRange().getValues();

    var sudahAda = false;
    for (var i = 0; i < dataLogin.length; i++) {
      if (dataLogin[i][0] && dataLogin[i][0].toString().toLowerCase().trim() === email.toString().toLowerCase().trim()) {
        sudahAda = true;
        break;
      }
    }

    if (!sudahAda) {
      var batch = sheet.getRange(row, 5).getValue(); // Kolom E: Batch/Kelas
      var linkDashboard = "https://taxcenteruinbandung.com/siswa/dashboard";
      sheetLogin.appendRow([email, passDefault, "SISWA", nama, linkDashboard, "", batch]); // Ditambah kolom sertifikat kosong dan batch/kelas

      var subjek = "Pendaftaran Berhasil! Akses Login LMS Brevet Pajak Batch 6";

      var pesanTeks = "Halo " + nama + ",\n\nPembayaran Anda telah diverifikasi. Berikut adalah akses LMS Anda:\n" +
        "Username: " + email + "\n" +
        "Password: " + passDefault + "\n\n" +
        "Login di: https://taxcenteruinbandung.com/\n\n" +
        "Mohon segera bergabung ke grup WA peserta: https://chat.whatsapp.com/Bde2f55PsNZ9G55wYx0VxS?mode=gi_t\n\n" +
        "Salam Hormat,\nPanitia Tax Center FISIP UIN SGD Bandung";

      var pesanHTML = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="background-color: #1A3365; padding: 25px; text-align: center;">
          <h2 style="color: #ffffff; margin: 0; font-size: 22px;">Pendaftaran Berhasil!</h2>
        </div>
        <div style="padding: 30px; background-color: #ffffff; color: #333333; line-height: 1.6; font-size: 15px;">
          <p>Yth. <strong>${nama}</strong>,</p>
          <p>Selamat! Pembayaran Anda telah kami terima dan verifikasi. Anda kini resmi terdaftar sebagai peserta <strong>Pelatihan Brevet Pajak Batch 6</strong> yang diselenggarakan oleh Tax Center FISIP UIN Sunan Gunung Djati Bandung.</p>
          
          <div style="background-color: #f8fafc; border-left: 4px solid #FFBB00; padding: 15px 20px; margin: 25px 0;">
            <h3 style="margin-top: 0; color: #1A3365; font-size: 16px;">Detail Akun LMS Anda</h3>
            <p style="margin: 8px 0;"><strong>Username:</strong> ${email}</p>
            <p style="margin: 8px 0;"><strong>Password:</strong> <span style="background:#e2e8f0; padding:4px 10px; border-radius:4px; font-family:monospace; font-weight:bold; letter-spacing:1px; color:#1A3365;">${passDefault}</span></p>
          </div>

          <p>Silakan gunakan akun di atas untuk masuk ke portal pembelajaran (LMS) kami melalui tautan berikut:</p>
          <p style="text-align: center; margin: 30px 0;">
            <a href="https://taxcenteruinbandung.com/" style="background-color: #1A3365; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Masuk ke LMS Tax Center</a>
          </p>

          <p style="text-align: center; margin: 25px 0;">
            <a href="https://chat.whatsapp.com/Bde2f55PsNZ9G55wYx0VxS?mode=gi_t" style="background-color: #25D366; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Bergabung ke Grup WhatsApp</a>
          </p>

          <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
          <p style="font-size: 13px; color: #64748b; margin-bottom: 0; text-align: center;">Salam Hormat,<br><strong>Panitia Tax Center FISIP UIN SGD Bandung</strong></p>
        </div>
      </div>
      `;

      GmailApp.sendEmail(email, subjek, pesanTeks, {
        htmlBody: pesanHTML
      });
      sheet.getRange(row, 10).setValue("AKTIF (Akun Dibuat)");
      clearLaravelCache();
    } else {
      sheet.getRange(row, 10).setValue("AKTIF (Akun Sudah Ada)");
      clearLaravelCache();
    }
  }
}

// ============================================================
// 4. ROBOT BLAST JADWAL VIA EMAIL & WA (TETAP INTAK)
// ============================================================
function blastJadwalOtomatis(e) {
  var sheet = e.source.getActiveSheet();
  var range = e.range;
  var row = range.getRow();

  if (sheet.getName() != "JADWAL_KELAS" || row <= 1) {
    return;
  }

  // Ambil semua data pada baris tersebut (Kolom 1 sampai 7)
  var rowValues = sheet.getRange(row, 1, 1, 7).getValues()[0];
  var tanggal = rowValues[0];
  var matkul = rowValues[1];
  var jam = rowValues[2];
  var dosen = rowValues[3];
  var linkZoom = rowValues[5];
  var aksi = rowValues[6];

  // Cek apakah data lengkap (Tanggal, Sesi/Materi, Jam, Dosen, Link Zoom)
  var isComplete = (tanggal !== "" && matkul !== "" && jam !== "" && dosen !== "" && linkZoom !== "");

  // Cek apakah aksi manual atau otomatis (kolom G kosong)
  var isManualBlast = (range.getColumn() == 7 && (e.value == "BLAST" || range.getValue() == "BLAST"));
  var isAutoBlast = (aksi === "");

  if (isComplete && (isAutoBlast || isManualBlast)) {
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheetPeserta = ss.getSheetByName("DATABASE_PESERTA");
    var dataPeserta = sheetPeserta.getDataRange().getValues();
    var emailList = [];

    for (var i = 1; i < dataPeserta.length; i++) {
      if (dataPeserta[i][2] != "" && dataPeserta[i][7] == "LUNAS") emailList.push(dataPeserta[i][2]);
    }

    if (emailList.length > 0) {
      blastJadwalEmail(matkul, tanggal, jam, linkZoom, emailList);
      sheet.getRange(row, 7).setValue("✅ TERKIRIM KE " + emailList.length + " SISWA");

      // Kirim WA blast otomatis
      var waMsg = "📢 *INFO JADWAL BARU*\n\n" +
        "Mata Pelajaran: *" + matkul + "*\n" +
        "Hari/Tanggal: " + tanggal + "\n" +
        "Jam: " + jam + " WIB\n" +
        "Dosen: " + dosen + "\n" +
        "Link Zoom: " + linkZoom + "\n\n" +
        "Silakan ikuti sesi kelas tepat waktu melalui LMS!";
      sendWaFonnte(waMsg);
    } else {
      sheet.getRange(row, 7).setValue("⚠️ Gagal: Tidak ada siswa berstatus LUNAS");
    }
  }
}

function notifJadwalOtomatis(e) {
  var sheet = e.source.getActiveSheet();
  var range = e.range;
  var row = range.getRow();
  var col = range.getColumn();

  if (sheet.getName() == "JADWAL_KELAS" && col == 7 && range.getValue() == "KIRIM") {
    var mapel = sheet.getRange(row, 2).getValue();    // Column B: Materi
    var dosen = sheet.getRange(row, 4).getValue();    // Column D: Pemateri
    var jam = sheet.getRange(row, 3).getValue();      // Column C: Waktu
    var link = sheet.getRange(row, 6).getValue();     // Column F: Link Zoom

    var pesan = "📢 *INFO JADWAL BARU*\n\n" +
      "Mata Pelajaran: *" + mapel + "*\n" +
      "Dosen: " + dosen + "\n" +
      "Jam: " + jam + "\n" +
      "Link: " + link + "\n\n" +
      "Cek detailnya di LMS ya!";

    sendWaFonnte(pesan);
    sheet.getRange(row, 7).setValue("SENT ✅");
  }
}

function sendWaFonnte(pesan) {
  try {
    var token = "dGEgjY87Zd7NUez7yi91";
    var target = "120363411047186180@g.us";

    // Jika token masih default/placeholder, lewati dengan aman agar tidak error
    if (token === "TOKEN_FONNTE_LU_DI_SINI" || target === "ID_GRUP_WA_LU_DI_SINI") {
      console.log("Fonnte WA Blast dilewati karena token/target belum diatur.");
      return;
    }

    var options = {
      "method": "post",
      "payload": {
        "target": target,
        "message": pesan
      },
      "headers": {
        "Authorization": token
      },
      "muteHttpExceptions": true
    };
    UrlFetchApp.fetch("https://api.fonnte.com/send", options);
    console.log("Request WA Fonnte berhasil dikirim.");
  } catch (err) {
    console.log("Gagal memanggil API Fonnte: " + err.toString());
  }
}

// ============================================================
// UTILITIES
// ============================================================
function resJSON(data) {
  return ContentService.createTextOutput(JSON.stringify(data)).setMimeType(ContentService.MimeType.JSON);
}

function pancingIzin() {
  DriveApp.getFolderById(DRIVE_FOLDER_ID);
  try { DriveApp.getFolderById(DRIVE_MATERI_FOLDER_ID); } catch (e) { }
  SpreadsheetApp.getActiveSpreadsheet().getSheetByName("SUBMISSION_TUGAS");
  SpreadsheetApp.getActiveSpreadsheet().getSheetByName("MATERI_BELAJAR");
  console.log("Izin berhasil didapatkan!");
}

// ============================================================
// WEBHOOK INTEGRASI - MEMBERSIHKAN CACHE LARAVEL
// ============================================================
function clearLaravelCache() {
  try {
    var url = LARAVEL_APP_URL.replace(/\/$/, "") + "/api/webhook/clear-cache";
    var payload = {
      "token": SECURE_TOKEN
    };
    var options = {
      "method": "post",
      "contentType": "application/json",
      "payload": JSON.stringify(payload),
      "muteHttpExceptions": true
    };
    UrlFetchApp.fetch(url, options);
    console.log("Webhook pembersihan cache berhasil dikirim ke Laravel.");
  } catch (err) {
    console.log("Gagal membersihkan cache Laravel via Webhook: " + err.toString());
  }
}

// ============================================================
// PEMICU EDIT MANUAL DI SPREADSHEET (Auto Cache Invalidation)
// ============================================================
function onEdit(e) {
  try {
    var sheet = e.source.getActiveSheet();
    var sheetName = sheet.getName();

    // Daftarkan tab yang mempengaruhi data di LMS jika diedit secara manual
    var monitoredSheets = ["JADWAL_KELAS", "MATERI_BELAJAR", "TUGAS_KELAS", "SUBMISSION_TUGAS", "DATA_LOGIN"];

    if (monitoredSheets.indexOf(sheetName) !== -1) {
      clearLaravelCache();
    }

    // Jalankan fungsi otomatisasi berdasarkan tab yang diedit
    if (sheetName == "JADWAL_KELAS") {
      blastJadwalOtomatis(e);
      notifJadwalOtomatis(e);
    }

    if (sheetName == "DATABASE_PESERTA") {
      kirimEmailOtomatis(e);
    }
  } catch (err) {
    console.log("Error pada onEdit trigger: " + err.toString());
  }
}

// ============================================================
// EMAIL NOTIFIKASI NILAI TUGAS
// ============================================================
function sendNotificationEmail(email, idTugas, nilai, feedback) {
  try {
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheetLogin = ss.getSheetByName("DATA_LOGIN");
    var dataLogin = sheetLogin.getDataRange().getValues();
    var nama = "Peserta";
    for (var i = 0; i < dataLogin.length; i++) {
      if (dataLogin[i][0] == email) {
        nama = dataLogin[i][3] || "Peserta";
        break;
      }
    }

    var subjek = "Nilai Tugas Baru Telah Dirilis: " + idTugas;
    var feedbackText = feedback ? feedback : "-";

    var pesanTeks = "Halo " + nama + ",\n\nTugas Anda (" + idTugas + ") telah dinilai oleh Dosen.\n" +
      "Nilai: " + nilai + "\n" +
      "Catatan/Feedback: " + feedbackText + "\n\n" +
      "Silakan cek rekap nilai lengkap Anda di LMS: " + LARAVEL_APP_URL.replace(/\/$/, "") + "/siswa/dashboard?tab=nilai\n\n" +
      "Salam,\nPanitia Tax Center FISIP UIN SGD Bandung";

    var pesanHTML = `
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
      <div style="background-color: #1A3365; padding: 20px; text-align: center;">
        <h2 style="color: #ffffff; margin: 0; font-size: 18px;">Tugas Anda Telah Dinilai!</h2>
      </div>
      <div style="padding: 25px; background-color: #ffffff; color: #333333; line-height: 1.6; font-size: 14px;">
        <p>Halo <strong>${nama}</strong>,</p>
        <p>Tugas Anda untuk kode <strong>${idTugas}</strong> telah dinilai oleh Dosen dengan detail berikut:</p>
        
        <div style="background-color: #f8fafc; border-left: 4px solid #FFBB00; padding: 15px; margin: 20px 0; border-radius: 4px;">
          <p style="margin: 5px 0;"><strong>Nilai:</strong> <span style="font-size: 16px; font-weight: bold; color: #16a34a;">${nilai}</span></p>
          <p style="margin: 5px 0;"><strong>Catatan/Feedback:</strong> ${feedbackText}</p>
        </div>
        
        <p>Silakan masuk ke dashboard siswa untuk melihat rekap nilai lengkap Anda:</p>
        <p style="text-align: center; margin: 25px 0;">
          <a href="${LARAVEL_APP_URL.replace(/\/$/, "")}/siswa/dashboard?tab=nilai" style="background-color: #1A3365; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Lihat Rekap Nilai</a>
        </p>
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 25px 0;">
        <p style="font-size: 11px; color: #64748b; text-align: center; margin-bottom: 0;">Salam Hormat,<br><strong>Panitia Tax Center FISIP UIN SGD Bandung</strong></p>
      </div>
    </div>
    `;

    GmailApp.sendEmail(email, subjek, pesanTeks, { htmlBody: pesanHTML });
    console.log("Email notifikasi nilai terkirim ke " + email);
  } catch (e) {
    console.log("Gagal mengirim email notifikasi nilai: " + e.toString());
  }
}

// ============================================================
// EMAIL BLAST TUGAS BARU
// ============================================================
function blastTugasOtomatis(idTugas, mapel, judul, deskripsi, linkSoal, deadline) {
  try {
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheetPeserta = ss.getSheetByName("DATABASE_PESERTA");
    var dataPeserta = sheetPeserta.getDataRange().getValues();
    var emailList = [];

    for (var i = 1; i < dataPeserta.length; i++) {
      if (dataPeserta[i][2] != "" && dataPeserta[i][7] == "LUNAS") {
        emailList.push(dataPeserta[i][2]);
      }
    }

    if (emailList.length > 0) {
      var subjek = "TUGAS BARU: " + idTugas + " - " + mapel;

      var deskripsiText = deskripsi ? deskripsi : "-";
      var linkText = linkSoal ? linkSoal : "Lihat di dashboard";
      var deadlineText = deadline ? deadline : "-";

      var pesanTeks = "Halo Peserta Brevet,\n\nAda tugas baru yang telah dirilis!\n" +
        "ID Tugas: " + idTugas + "\n" +
        "Mata Pelajaran: " + mapel + "\n" +
        "Judul Tugas: " + judul + "\n" +
        "Deskripsi: " + deskripsiText + "\n" +
        "Deadline: " + deadlineText + "\n" +
        "Link Soal: " + linkText + "\n\n" +
        "Silakan kerjakan dan kumpulkan sebelum batas waktu berakhir di dashboard LMS: " + LARAVEL_APP_URL.replace(/\/$/, "") + "/siswa/dashboard\n\n" +
        "Salam,\nPanitia Tax Center FISIP UIN SGD Bandung";

      var pesanHTML = `
      <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="background-color: #1A3365; padding: 25px; text-align: center;">
          <h2 style="color: #ffffff; margin: 0; font-size: 20px;">📢 Penugasan Baru Dirilis!</h2>
        </div>
        <div style="padding: 30px; background-color: #ffffff; color: #333333; line-height: 1.6; font-size: 15px;">
          <p>Halo Peserta Brevet Pajak,</p>
          <p>Tutor telah merilis tugas baru untuk mata pelajaran <strong>${mapel}</strong>. Berikut adalah rincian penugasan Anda:</p>
          
          <div style="background-color: #f8fafc; border-left: 4px solid #FFBB00; padding: 15px 20px; margin: 25px 0; border-radius: 4px;">
            <p style="margin: 8px 0;"><strong>ID Tugas:</strong> ${idTugas}</p>
            <p style="margin: 8px 0;"><strong>Judul Tugas:</strong> ${judul}</p>
            <p style="margin: 8px 0;"><strong>Batas Waktu (Deadline):</strong> <span style="color: #e11d48; font-weight: bold;">${deadlineText}</span></p>
            <p style="margin: 8px 0;"><strong>Deskripsi:</strong> ${deskripsiText}</p>
          </div>

          ${linkSoal ? `
          <p>Unduh lembar soal melalui tautan di bawah ini:</p>
          <p style="text-align: center; margin: 25px 0;">
            <a href="${linkSoal}" style="background-color: #e11d48; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Unduh Berkas Soal</a>
          </p>
          ` : ''}

          <p>Silakan kerjakan tugas ini secara mandiri dan kumpulkan tepat waktu melalui portal LMS:</p>
          <p style="text-align: center; margin: 25px 0;">
            <a href="${LARAVEL_APP_URL.replace(/\/$/, "")}/siswa/dashboard" style="background-color: #1A3365; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Kumpulkan di LMS</a>
          </p>
          
          <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
          <p style="font-size: 13px; color: #64748b; margin-bottom: 0; text-align: center;">Salam Hormat,<br><strong>Panitia Tax Center FISIP UIN SGD Bandung</strong></p>
        </div>
      </div>
      `;

      GmailApp.sendEmail(Session.getActiveUser().getEmail(), subjek, pesanTeks, {
        htmlBody: pesanHTML,
        bcc: emailList.join(",")
      });

      return "✅ NOTIFIKASI TERKIRIM KE " + emailList.length + " SISWA";
    }
    return "⚠️ Gagal: Tidak ada siswa berstatus LUNAS";
  } catch (err) {
    console.log("Error pada blastTugasOtomatis: " + err.toString());
    return "⚠️ Gagal: " + err.toString();
  }
}

// ============================================================
// EMAIL BLAST JADWAL BARU
// ============================================================
function blastJadwalEmail(matkul, tanggal, jam, linkZoom, emailList) {
  try {
    var subjek = "JADWAL KELAS BARU: " + matkul;

    var pesanTeks = "Halo Peserta Brevet,\n\nAda jadwal kelas baru yang telah diatur!\n" +
      "Mata Pelajaran: " + matkul + "\n" +
      "Hari/Tanggal: " + tanggal + "\n" +
      "Waktu: " + jam + " WIB\n" +
      "Link Zoom: " + linkZoom + "\n\n" +
      "Silakan ikuti kelas tepat waktu melalui portal LMS: " + LARAVEL_APP_URL.replace(/\/$/, "") + "/siswa/dashboard\n\n" +
      "Salam,\nPanitia Tax Center FISIP UIN SGD Bandung";

    var pesanHTML = `
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
      <div style="background-color: #1A3365; padding: 25px; text-align: center;">
        <h2 style="color: #ffffff; margin: 0; font-size: 20px;">🗓️ Jadwal Kelas Baru Dirilis!</h2>
      </div>
      <div style="padding: 30px; background-color: #ffffff; color: #333333; line-height: 1.6; font-size: 15px;">
        <p>Halo Peserta Brevet Pajak,</p>
        <p>Panitia telah merilis jadwal pertemuan kelas terbaru. Berikut adalah rincian jadwal sesi kuliah Anda:</p>
        
        <div style="background-color: #f8fafc; border-left: 4px solid #FFBB00; padding: 15px 20px; margin: 25px 0; border-radius: 4px;">
          <p style="margin: 8px 0;"><strong>Mata Pelajaran:</strong> ${matkul}</p>
          <p style="margin: 8px 0;"><strong>Hari/Tanggal:</strong> ${tanggal}</p>
          <p style="margin: 8px 0;"><strong>Waktu (Jam):</strong> <span style="font-weight: bold; color: #1A3365;">${jam} WIB</span></p>
        </div>

        <p>Anda dapat langsung mengikuti tatap muka kelas online melalui Zoom dengan menekan tombol di bawah ini:</p>
        <p style="text-align: center; margin: 25px 0;">
          <a href="${linkZoom}" style="background-color: #e11d48; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Masuk Kelas Zoom</a>
        </p>

        <p>Atau akses dashboard siswa untuk melihat rekaman & materi kelas lainnya:</p>
        <p style="text-align: center; margin: 20px 0;">
          <a href="${LARAVEL_APP_URL.replace(/\/$/, "")}/siswa/dashboard" style="background-color: #475569; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block; font-size: 13px;">Buka Dashboard LMS</a>
        </p>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        <p style="font-size: 13px; color: #64748b; margin-bottom: 0; text-align: center;">Salam Hormat,<br><strong>Panitia Tax Center FISIP UIN SGD Bandung</strong></p>
      </div>
    </div>
    `;

    GmailApp.sendEmail(Session.getActiveUser().getEmail(), subjek, pesanTeks, {
      htmlBody: pesanHTML,
      bcc: emailList.join(",")
    });
  } catch (err) {
    console.log("Error pada blastJadwalEmail: " + err.toString());
  }
}

// ============================================================
// 5. ROBOT PENGINGAT DEADLINE TUGAS (DAILY TIME-DRIVEN)
// ============================================================
function sendDeadlineReminders() {
  try {
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    var sheetTugas = ss.getSheetByName("TUGAS_KELAS");
    if (!sheetTugas) return;
    var dataTugas = sheetTugas.getDataRange().getValues();

    var sheetSub = ss.getSheetByName("SUBMISSION_TUGAS");
    var dataSub = sheetSub ? sheetSub.getDataRange().getValues() : [];

    var sheetLogin = ss.getSheetByName("DATA_LOGIN");
    var dataLogin = sheetLogin.getDataRange().getValues();

    // Ambil daftar email siswa aktif
    var students = [];
    for (var i = 1; i < dataLogin.length; i++) {
      var email = dataLogin[i][0];
      var role = dataLogin[i][2];
      var nama = dataLogin[i][3] || "Peserta";
      if (email && role && (role.toString().toUpperCase() === "SISWA" || role.toString().toUpperCase() === "PESERTA")) {
        students.push({
          email: email.toString().toLowerCase().trim(),
          nama: nama
        });
      }
    }

    var now = new Date();

    for (var i = 1; i < dataTugas.length; i++) {
      var idTugas = dataTugas[i][0];
      var mapel = dataTugas[i][1];
      var judul = dataTugas[i][2];
      var deadlineStr = dataTugas[i][5];
      var reminderStatus = dataTugas[i][6] || ""; // Kolom G (Reminder Status)

      if (!idTugas || !deadlineStr || reminderStatus === "SENT") continue;

      var deadline = new Date(deadlineStr);
      if (isNaN(deadline.getTime())) continue;

      // Hitung selisih waktu dalam jam
      var diffMs = deadline.getTime() - now.getTime();
      var diffHours = diffMs / (1000 * 60 * 60);

      // Jika deadline dalam waktu <= 24 jam ke depan dan belum expired
      if (diffHours > 0 && diffHours <= 24) {
        // Ambil daftar siswa yang sudah mengumpulkan
        var submittedEmails = {};
        for (var j = 1; j < dataSub.length; j++) {
          if (dataSub[j][3] == idTugas) {
            var sEmail = dataSub[j][1].toString().toLowerCase().trim();
            submittedEmails[sEmail] = true;
          }
        }

        var countSent = 0;
        for (var k = 0; k < students.length; k++) {
          var studentEmail = students[k].email;
          if (!submittedEmails[studentEmail]) {
            sendReminderEmail(students[k].email, students[k].nama, idTugas, mapel, judul, deadlineStr);
            countSent++;
          }
        }

        // Tandai reminder telah dikirim
        sheetTugas.getRange(i + 1, 7).setValue("SENT");
        console.log("Berhasil mengirim " + countSent + " pengingat deadline untuk tugas: " + idTugas);
      }
    }
  } catch (err) {
    console.log("Error pada sendDeadlineReminders: " + err.toString());
  }
}

function sendReminderEmail(email, nama, idTugas, mapel, judul, deadlineStr) {
  try {
    var subjek = "PENGINGAT DEADLINE: Tugas " + idTugas + " - " + mapel;

    var pesanTeks = "Halo " + nama + ",\n\n" +
      "Tugas Anda untuk mata pelajaran " + mapel + " dengan ID " + idTugas + " akan segera mencapai tenggat waktu.\n" +
      "Judul Tugas: " + judul + "\n" +
      "Tenggat Waktu: " + deadlineStr + "\n\n" +
      "Kami mendeteksi bahwa Anda belum mengumpulkan tugas ini. Silakan kerjakan dan kumpulkan segera melalui LMS sebelum batas waktu berakhir:\n" +
      LARAVEL_APP_URL.replace(/\/$/, "") + "/siswa/dashboard\n\n" +
      "Salam,\nPanitia Tax Center FISIP UIN SGD Bandung";

    var pesanHTML = `
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
      <div style="background-color: #e11d48; padding: 20px; text-align: center;">
        <h2 style="color: #ffffff; margin: 0; font-size: 18px;">⚠️ Pengingat Tenggat Waktu Tugas</h2>
      </div>
      <div style="padding: 25px; background-color: #ffffff; color: #333333; line-height: 1.6; font-size: 14px;">
        <p>Halo <strong>${nama}</strong>,</p>
        <p>Tugas Anda untuk mata pelajaran <strong>${mapel}</strong> akan segera mencapai batas waktu pengumpulan.</p>
        
        <div style="background-color: #fff1f2; border-left: 4px solid #e11d48; padding: 15px; margin: 20px 0; border-radius: 4px;">
          <p style="margin: 5px 0;"><strong>ID Tugas:</strong> ${idTugas}</p>
          <p style="margin: 5px 0;"><strong>Judul Tugas:</strong> ${judul}</p>
          <p style="margin: 5px 0;"><strong>Tenggat Waktu:</strong> <span style="color: #e11d48; font-weight: bold;">${deadlineStr}</span></p>
        </div>
        
        <p>Kami mencatat bahwa Anda **belum mengumpulkan** tugas ini. Mohon segera selesaikan dan kumpulkan melalui portal LMS sebelum waktu habis:</p>
        <p style="text-align: center; margin: 25px 0;">
          <a href="${LARAVEL_APP_URL.replace(/\/$/, "")}/siswa/dashboard" style="background-color: #e11d48; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">Kumpulkan Sekarang</a>
        </p>
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 25px 0;">
        <p style="font-size: 11px; color: #64748b; text-align: center; margin-bottom: 0;">Salam Hormat,<br><strong>Panitia Tax Center FISIP UIN SGD Bandung</strong></p>
      </div>
    </div>
    `;

    GmailApp.sendEmail(email, subjek, pesanTeks, { htmlBody: pesanHTML });
  } catch (e) {
    console.log("Gagal mengirim email reminder ke " + email + ": " + e.toString());
  }
}

function createTimeDrivenTriggers() {
  var triggers = ScriptApp.getProjectTriggers();
  for (var i = 0; i < triggers.length; i++) {
    if (triggers[i].getHandlerFunction() === "sendDeadlineReminders") {
      ScriptApp.deleteTrigger(triggers[i]);
    }
  }

  // Daftarkan trigger harian baru pada jam 8 pagi
  ScriptApp.newTrigger("sendDeadlineReminders")
    .timeBased()
    .everyDays(1)
    .atHour(8)
    .create();

  console.log("Trigger sendDeadlineReminders harian jam 8 pagi berhasil didaftarkan.");
}

// ============================================================
// FUNGSI PEMBANTU UNTUK MENCARI ID GRUP WHATSAPP
// ============================================================
function listMyWhatsAppGroups() {
  var token = "dGEgjY87Zd7NUez7yi91"; // Ganti dengan token Fonnte Anda jika ingin tes langsung di Apps Script
  
  if (token === "TOKEN_FONNTE_LU_DI_SINI") {
    Logger.log("Silakan isi Token Fonnte Anda terlebih dahulu pada kode di atas!");
    return;
  }
  
  try {
    Logger.log("1. Meminta Fonnte memindai daftar grup terbaru di HP Anda...");
    var fetchOptions = {
      "method": "post",
      "headers": {
        "Authorization": token
      },
      "muteHttpExceptions": true
    };
    UrlFetchApp.fetch("https://api.fonnte.com/fetch-group", fetchOptions);
    
    // Tunggu 3 detik agar Fonnte memproses pembacaan grup
    Utilities.sleep(3000);
    
    Logger.log("2. Mengambil daftar ID Grup WhatsApp dari Fonnte...");
    var getOptions = {
      "method": "post",
      "headers": {
        "Authorization": token
      },
      "muteHttpExceptions": true
    };
    var response = UrlFetchApp.fetch("https://api.fonnte.com/get-whatsapp-group", getOptions);
    var resData = JSON.parse(response.getContentText());
    
    if (resData.status && resData.data && resData.data.length > 0) {
      Logger.log("==================================================");
      Logger.log("DITEMUKAN " + resData.data.length + " GRUP WHATSAPP:");
      Logger.log("==================================================");
      for (var i = 0; i < resData.data.length; i++) {
        Logger.log("Nama Grup : " + resData.data[i].name);
        Logger.log("ID Grup   : " + resData.data[i].id);
        Logger.log("--------------------------------------------------");
      }
    } else {
      Logger.log("Respons Fonnte: " + response.getContentText());
      Logger.log("Gagal mengambil grup. Pastikan nomor WhatsApp di Fonnte sudah terhubung (Connected) dan sudah masuk ke dalam grup.");
    }
  } catch (err) {
    Logger.log("Terjadi error: " + err.toString());
  }
}


