## Spark v0.5 - Files and Jsons

The base content in Listiary are the Describe source files - that users edit, and their JSON representations 
that those files are compiled to, when saved.



## Functions
`file-view` - Output the contents of a single file.
`file-download` - Download a Describe file.
`file-upload` - Upload a Describe file to the database.
`file-compile` - Compile a single file in the database to JSON.
`file-delete` - Delete a single file.
<br><br>

`files-list` - List the files in the database.
`files-count` - Count the files in the database.
`files-download` - Download Describe files from the database.
`files-upload` - Upload Describe files to the database.
`files-compile` - Compile all Describe files in the database, thus populating the JSONs table in the database.
`files-delete` - Delete all the Describe files from the database.
<br><br>

`json-view` - Output the contents of a single json file.
`json-download` - Download a JSON file.
`json-upload` - Upload given data representing a JSON file to the database.
`json-delete` - Delete a single JSON file.
<br><br>

`jsons-list` - List the JSON files in the database.
`jsons-count` - Count the JSON files in the database.
`jsons-download` - Download JSON files from the database.
`jsons-delete` - Delete all the JSON files from the database.
<br><br>