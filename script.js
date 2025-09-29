document.getElementById('add-subscription-form').addEventListener('submit', function (e) {
  e.preventDefault(); // ⛔ prevent full-page refresh

  const subscription_name = document.getElementById('subscription_name').value;
  const start_date = document.getElementById('start_date').value;
  const payment_due_date = document.getElementById('payment_due_date').value;
  const price = document.getElementById('price').value;
  const status = document.getElementById('status').value;

  fetch('add_subscription.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      subscription_name,
      start_date,
      payment_due_date,
      price,
      status
    })
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message || (data.success ? 'Subscription added!' : 'Error'));
  })
  .catch(error => {
    console.error('AJAX Error:', error);
    alert('AJAX error occurred');
  });
});
