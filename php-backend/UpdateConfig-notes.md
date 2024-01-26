# AMI UpdateConfig Implementation Notes

- The editor allows adding duplicate Vars. Check for existing Vars and Update as necessary.
- Duplicate Categories are optionally allowed.
- Deleting a Var leaves an empty line.
- New Categories are added to the end of the file.
  - If the last line in a file is a comment, an added Category incorectly starts at the end of the line, not the end of the file.
- Deleting a Category removes all blank lines and comments above it.
- Indented comments leading space is removed.
- The AMI won't set a var if the value matches the template.
