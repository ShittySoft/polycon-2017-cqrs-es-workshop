Feature: check-in and check-out

  Scenario: Check-in
    Given a building was registered
    When the user checks into the building
    Then the user was checked into the building

  Scenario: Double check-in causes an anomaly to be detected
    Given a building was registered
    And the user checked into the building
    When the user checks into the building
    Then the user was checked into the building
    And a check-in anomaly was detected

  Scenario: check-in (with examples)
    Given the "Hilton hotel xyz" has been registered as a building
    When "bob" checks into the building
    Then the "bob" was checked into the building
