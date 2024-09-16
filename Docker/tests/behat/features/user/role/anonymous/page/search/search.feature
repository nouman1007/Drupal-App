@anonymous @search @basic

Feature: Basic search functionality

  Scenario: Anonymous user should be able to find a large set of results
    Given I am on "/search"
    And I fill in "keywords" with "new york"
    And I press "Search"
    Then I should see the pattern "\d+ results"
    And I should not see "No results found."

  Scenario: Anonymous user should be able to find a large set of results with a quoted query
    Given I am on "/search"
    And I fill in "keywords" with "\"new york\""
    And I press "Search"
    Then I should see the pattern "\d+ results"
    And I should not see "No results found."
    And I should not see "&quot;new york&quot;"

  Scenario: Anonymous user should not be able to find any results with an obscure quoted query
    Given I am on "/search"
    And I fill in "keywords" with "\"new york yankees\""
    And I press "Search"
    Then I should see "No results found."
    And I should not see the pattern "\d+ results"
    And I should not see "&quot;new york yankees&quot;"
