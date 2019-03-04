window.addEventListener("load", function () {
            function sendData() {
                var xhr = new XMLHttpRequest();

                // Bind the FormData object and the form element
                var formData = new FormData(form);

                // Define what happens on successful data submission
                xhr.addEventListener("load", function (event) {
                    var response = JSON.parse(event.target.responseText);
                    alert(response.message);
                });

                // Define what happens in case of error
                xhr.addEventListener("error", function (event) {
                    alert('Oops! Something went wrong.');
                });

                // Set up our request
                xhr.open("POST", "contact-submit.php");

                // The data sent is what the user provided in the form
                xhr.send(formData);
            }

            // Access the form element...
            var form = document.getElementById("myForm");

            // ...and take over its submit event.
            form.addEventListener("submit", function (event) {
                event.preventDefault();

                sendData();
            });
        });