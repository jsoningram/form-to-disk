# f2d
WordPress plugin to write form submissions to file and email the file using wp_cron

## Form Action
`marker` is a hidden field used to prevent other forms from writing data to file

```
function formToDisk() {
	var firstName = $('#name').val();
	var lastName  = $('#surname').val();
	var email     = $('#email').val();
	var marker    = $('#marker').val();

	var data = {
		marker: marker,
		email: email,
		firstName: firstName,
		lastName: lastName,
		action: 'form_to_disk'
	};

	if (firstName && lastName && email) {
		$.ajax({
			type: 'POST',
			url: ajaxUrl',
			data: data,
			error: function() {
				// handle error
			},
			success: function(data) {
				// handle success
			}
		});
	}
}
```
