// In manage_networks
$('#addNetworkDescription').keyup(validateInput);
$('#networkDescription').keyup(validateInput);
// In user_pages
$('#userHostDescription').keyup(validateInput);
// In book_address
$('.host-description').keyup(validateInput);

function validateInput() {
	var error_msg = "Only whitespace characters is not valid input.";
	var text = $(this).val().replace('\n', ' ').trim();
	if (typeof this.setCustomValidity === 'function') {
		this.setCustomValidity((text === '') ? error_msg : '');
	} else {
		if (text === '') {
			$(this).attr('title', error_msg);
		} else {
			$(this).removeAttr('title');
		}
	}
}