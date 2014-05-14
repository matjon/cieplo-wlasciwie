Feature: Static pages
  I want to see some static pages. Now.

  Scenario: Go to homepage
    Given I am on homepage
     Then I should see "Czym i za ile ogrzewać dom"
      And I should see "Sprawdź swój dom!"

  Scenario: Go to 'what's that' page
    Given I go to "/co_to_jest"
     Then I should see "Co to jest"
      And I should see "Kto za tym stoi"

  Scenario: Go to 'how it works' page
    Given I go to "/jak-to-dziala"
     Then I should see "Do czego mi to potrzebne"
      And I should see "Kuchennym wejściem"

  Scenario: Go to 'rules' page
    Given I go to "/zasady"
     Then I should see "Niczego od ciebie nie chcemy"
      And I should see "Strona używa ciasteczek"

  Scenario: Go to 'my results' page
    Given I go to "/moje-wyniki"
     Then I should see "Niestety, nic tu nie ma"
