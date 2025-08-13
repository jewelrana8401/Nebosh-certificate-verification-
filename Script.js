// Enable Next button only if checkbox is ticked
document.getElementById('agree').addEventListener('change', function() {
    document.getElementById('nextBtn').disabled = !this.checked;
});

function goToStep2() {
    window.location.href = "step2.html"; // Step 2 page
}
