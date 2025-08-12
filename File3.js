const certificates = {
    "FT6JJQLA8SPMZ3BKG8GDK2PY": { name: "John Doe", course: "NEBOSH IGC", date: "2024-05-20" },
    "ABCD123456": { name: "Jane Smith", course: "NEBOSH HSW", date: "2023-08-15" },
    "XYZ987654": { name: "Ali Ahmed", course: "NEBOSH IOGC", date: "2022-11-10" }
};

function validateCertificate() {
    const serial = document.getElementById("serialNumber").value.trim();
    const resultDiv = document.getElementById("result");

    if (certificates[serial]) {
        const cert = certificates[serial];
        resultDiv.innerHTML = `
            ✅ Certificate Found<br>
            Name: ${cert.name}<br>
            Course: ${cert.course}<br>
            Date: ${cert.date}
        `;
        resultDiv.style.color = "green";
    } else {
        resultDiv.innerHTML = "❌ Certificate Not Found";
        resultDiv.style.color = "red";
    }
}
