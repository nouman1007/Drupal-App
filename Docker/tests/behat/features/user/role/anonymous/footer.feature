@anonymous @footer @basic

Feature: Basic Footer functionality

  Scenario: Anonymous user should see expected text on the home page
    Given I am on the homepage
    # TODO: And I'm looking at the Footer of the page.
    Then I should see "Sign up to hear about our latest news, events, and opportunities."
    And I should see "Bringing Americans together to serve communities"
    And I should see "An official website of AmeriCorps. Produced and published at taxpayer expense."
