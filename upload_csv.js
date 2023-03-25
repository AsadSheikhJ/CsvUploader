function uploadExcelFile() {

    // get the selected file name from the drop-down menu
    let fileName = document.getElementById("fileSelect").value;

    // Get the selected file
    var fileInput = document.getElementById("excelFile");
    var file = fileInput.files[0];

    // Create a FormData object to send the file to the server
    var formData = new FormData();
    formData.append("excelFile", file);
    formData.append("selectedFile", fileName);

    // Send the file to the PHP script using AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "upload_excel.php", true);
    xhr.onload = function() {
        if (xhr.status == 200) {
            console.log(xhr.responseText);
        } else {
            console.log("Error: " + xhr.statusText);
        }
    };
    xhr.send(formData);
}