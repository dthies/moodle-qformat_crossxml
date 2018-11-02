@qformat @qformat_crossxml
Feature: Test importing questions with crossxml to change question type
  In order to reuse some questions
  As an teacher
  I need to be able to import them in an alternate form.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username | firstname |
      | teacher  | Teacher   |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And I log in as "teacher"
    And I follow "C1"

  @javascript @_file_upload
  Scenario: import XML file
    When I navigate to "Question bank > Import" in current page administration
    And I set the field "id_format_crossxml" to "1"
    And I upload "question/format/crossxml/tests/fixtures/questions.xml" file to "Import" filemanager
    And I press "id_submitbutton"
    Then I should see "Parsing questions from import file."
    And I should see "Importing 3 questions from file"
    When I press "Continue"
    Then I should see "Hannibal's animal"
