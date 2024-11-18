document.addEventListener('DOMContentLoaded', function() {
  const donationTypeElement = document.getElementById('donationType');
  const mpesaFields = document.getElementById('mpesaFields');
  const foodFields = document.getElementById('foodFields');
  const clothesFields = document.getElementById('clothesFields');
  const donationForm = document.getElementById('donationForm');

  donationTypeElement.addEventListener('change', function() {
      const donationType = donationTypeElement.value;
      mpesaFields.style.display = donationType === 'mpesa' ? 'block' : 'none';
      foodFields.style.display = donationType === 'food' ? 'block' : 'none';
      clothesFields.style.display = donationType === 'clothes' ? 'block' : 'none';
  });

  donationForm.addEventListener('submit', function(event) {
      const donationType = donationTypeElement.value;
      if (donationType === 'mpesa') {
          const mpesaNumber = document.getElementById('mpesaNumber').value;
          const amount = document.getElementById('amount').value;
          if (!mpesaNumber || !amount) {
              event.preventDefault();
              alert("Please enter both M-Pesa number and amount.");
          }
      }
  });
});
