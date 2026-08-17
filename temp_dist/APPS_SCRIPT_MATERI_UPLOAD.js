// Apps Script snippet: handle uploadMateriFile in doPost
// Paste into your existing doPost handler (ensure DRIVE_MATERI_FOLDER_ID is set)
if (data.action == 'uploadMateriFile') {
  try {
    var folder = DriveApp.getFolderById(DRIVE_MATERI_FOLDER_ID);
    var bytes = Utilities.base64Decode(data.base64);
    var blob = Utilities.newBlob(bytes, data.mimeType, data.fileName);
    var file = folder.createFile(blob);

    // Append to MATERI_BELAJAR sheet a new row if needed is handled by addMateri.
    // Return link to uploaded file
    return resJSON({ status: 'success', message: 'File uploaded', link: file.getUrl() });
  } catch (err) {
    return resJSON({ status: 'error', message: err.toString() });
  }
}