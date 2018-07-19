
> # READ THIS FIRST

> It is highly recommended for all contributors to update this file whenever there's a major update in source code. Use [this tool](https://stackedit.io/app#) for easy editing or [visit this page](https://help.github.com/articles/basic-writing-and-formatting-syntax/) for comprehensive guide on markdown syntax.

  

# Introduction

This package provides an easy way to seed roles and permissions from a csv file to database tables created by [Laratrust Package](https://github.com/santigarcor/laratrust).

### CAUTION
Following tables will be truncated on execution of this command:
- permission_user
- permission_role
- permissions
- role_user
- roles
  

## Signature
``` bash
roles-and-permissions:update {csvFilePath}
```
## CSV Format
### Columns
First 3 columns will have title as follow:
- permission_name
- permission_display_name
- permission_description

Next columns shall have the role `name` and `display_name` separated by semicolon `;` as follow:
`super-admin;Super Admin`

### Rows
Each row will have name of permissions, its display name and description respectively in first three cells. Following cells will have either `y` or `n` depending on the assignment of that permission to corresponding role.

### Sample
Here's a [Sample CSV File](https://docs.google.com/spreadsheets/d/1b_x1orATTR_QWxabVPg2Kg6WFqPmlwKT9RtfxuxYYtE/edit#gid=0).
  

# To-dos

Following are the approved items:

- Item-1

  

# Wishlist

Add the suggestions in this wishlist. Only approved wishlist items can be moved to To-dos list:

- Item-1