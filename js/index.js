document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const name = document.querySelector('input[name="name"]').value;
    const phone = document.querySelector('input[name="phone"]').value;
    const service = document.querySelector('select[name="service"]').value;
    const bookingDate = document.querySelector('input[name="booking_date"]').value;

    if (name && phone && service && bookingDate) {
        alert('Booking submitted successfully!');
        this.reset();
    } else {
        alert('Please fill in all fields');
    }
});