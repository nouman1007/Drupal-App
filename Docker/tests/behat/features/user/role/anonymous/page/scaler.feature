@anonymous @scaler @todo

Scenario: Anonymous user should see expected text on the SCALER page
    Given I am on "/grantees-sponsors/evaluation-resources/scaler"
    Then I should see "Scaling Checklists: Assessing Your Level of Evidence and Readiness (SCALER)"
    And I should see "The SCALER framework can help organizations improve the lives of more people by preparing them to successfully scale effective interventions. As shown in the figure above, the SCALER helps organizations (1) ensure the intervention to be scaled is likely to produce desired outcomes and is therefore worthy of being scaled and (2) identify whether the effective intervention and the organization are ready to scale."

Scenario: Anonymous user should see expected text on the SCALER page
    Given I am on "/grantees-sponsors/evaluation-resources/scaler-resources"
    # @todo Then I should see ""
