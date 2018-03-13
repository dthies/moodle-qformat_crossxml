qformat_crossxml
================

An input format plugin that interchanges some of the question types
in a Moodle XML export file.  It enables users to change types of some
questions in the Moodle question bank by exporting the questions as Moodle
XML format and importing with this format. Short answer questions will
be changed to multiple choice questions, and multiple choice questions
in the file will be imported as short answer questions. If the Drag and
Drop Matching matching question type is installed, those questions will be
switched with the standard matching type. If All-or-Nothing Multichoice or
OU Multiresonse are installed those types will be converted to regular
multichoice.

This directory should to question/format/crossxml in Moodle installation
directory. Login as admin to complete plugin installation.  Then select
this format *Cross XML* during question bank import.

All original files are copyright Daniel Thies 2018 dethies@gmail.com
and are licensed under the included GPL 3
