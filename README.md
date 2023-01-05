# FER
Drupal 9 FER (Family Event Registration) modules

● Form with the following data for a single registration:
○ Name of the employee (Free text, required)One plus (Yes/No, required)
○ Amount of kids (Number, required)
○ Amount of vegetarians (Number, required)
○ Email address (required)
● Untranslatable content type “Registration” with all the form fields. Nodes are created on submiting form and fills data fields of node. Employee name is a node title.
● Validating the form:
○ All required fields should be enforced
○ Only valid email addresses are allowed
○ Amount of vegetarians can not be higher than the total amount of people
○ An employee should not be able to register twice (with the same email address)
● Show the form on the path /registration/{department}

● New module with departments 
○ Config form with department fields
○ Automatically capture the department from the URL, f.e. /registration/finance will automatically
link the registration to the department “Finance
○ Add a department field to the content type and store the captured department on
newly created nodes (Department can be a textfield).
○ few departments are entered by defauld but not hardcoded
● Block with a registration count is shown on every page. Block is cached and Count number result is updated after every new registration.
