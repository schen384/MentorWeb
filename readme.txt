MentorWeb readme

MentorWeb is a web based application made for Georgia Tech students to better find mentors allowing them to better get a grasp of Georgia Tech campus and lifestyle and academic problems. 

-Spring 2016-

1.Aside from the setup below, if you are using sublime plugin for sftp connection, remember to ignore the sftp-config.json file before pushing your code
	steps:
		I have added it into the .gitignore and pushed to the repo
		
		Never mind.
		//1. open .gitignore at the root directory
		//2. add stfp-config.json at the end
		//3. save and close


2.There is a place where you might need to change the 'gtaccount' to your account name 
	json-gen/json-generator-user-template.js

  I don't know what this does as of right now and will update as soon as I figure it out.
  For now , I set this file to be ignored as well.



-Setup and Configuration-
1. Clone the repo onto your local machine
2. Follow the RNOC steps here, http://gtjourney.gatech.edu/gt-devhub/documentation for setting up the code on your prism account
3. Once the system is setup on the server, edit the files:
	config.php:
		replace each of the variables with your server and database credentials, see Getting Started on Google Drive
	js/config.js:
		replace each config variable with your specific info, see see Getting Started Google Drive if you are unsure
4. If you are not using the main database:
	4.1. Get the sql code from the Database Updates file on the Google Drive
	4.2. Run the sql code on your local database to get it up to data
		Note: this file can be run to update an existing local database as well

-Using MentorWeb-
1. Navigate to the homepage: http://dev.m.gatech.edu/d/yourGTID/w/MentorWeb/content/
2. Log in using your Georgia Tech login credentials
	If this is your first time entering the site:
	2.1. Select Mentor or Mentee
	2.2. Follow the instructions for regitration
	

Thanks,

Aidan Arrowood
Angel Dixon
Margo Osborne
Daniel Xiao
Ying Yao

Joe LeDoux

Albert Morlan, amorlan3, amorlan3@gatech.edu
JD Reddaway, jreddaway3, jdreddaway@gatech.edu
Mitchell Cox, mcox41, mcox41@gatech.edu
Mykal Thomas, mthomas46, mthomas46@gatech.edu