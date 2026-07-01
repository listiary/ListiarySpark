## Base content

The base content in Listiary are the Describe source files - that users edit,
and their JSON representations that those files are compiled to, when saved.

This article explains Spark CLI commands that deal with those entities.


## Single-item Actions


## Bulk actions
`files-count` - count the Describe files in the db
`files-list`- list all the Describe files in the db
`files-compile` - compile all... to JSON, store...
`files-download` - download all... to local folder...
`files-upload` - upload all files from a local folder..
`PRESENTATION_FLAGS` - see link here

```
files-count [PRESENTATION_FLAGS]
files-list [max=INT] [PRESENTATION_FLAGS]
files-compile [PRESENTATION_FLAGS]
files-download [max=INT] [PRESENTATION_FLAGS]
files-upload [PRESENTATION_FLAGS]
```

//Warning - returning the data from a user file can be vulnerable
//to command injections if we decide to do something with it later on