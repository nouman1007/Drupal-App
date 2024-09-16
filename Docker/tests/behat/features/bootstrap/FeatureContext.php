<?php
use Drupal\DrupalExtension\Context\DrupalContext;
use Behat\Behat\Tester\Exception\PendingException;
use Drupal\Core\Session\AccountInterface;
use PHPUnit\Framework\Assert;
use Drupal\user\Entity\User;

class FeatureContext extends DrupalContext
{
    private stdClass $testUser;

    /**
     * @Then I should see the pattern :pattern
     */
    public function iShouldSeeThePattern($pattern)
    {
        $pageContent = $this->getSession()->getPage()->getContent();
        if (!preg_match('/' . $pattern . '/', $pageContent)) {
            throw new \Exception(sprintf('The pattern "%s" was not found on the page.', $pattern));
        }
        Assert::assertTrue(true, sprintf('The pattern "%s" was found on the page.', $pattern));
    }

    /**
     * @Then I should not see the pattern :arg1
     */
    public function iShouldNotSeeThePattern($pattern)
    {
      $pageContent = $this->getSession()->getPage()->getContent();
      if (preg_match('/' . $pattern . '/', $pageContent)) {
          throw new \Exception(sprintf('The pattern "%s" was found on the page.', $pattern));
      }
      Assert::assertTrue(true, sprintf('The pattern "%s" was not found on the page.', $pattern));
    }

    public function assertAuthenticatedByRole($role)
    {
        // Check if a user with this role is already logged in.
        if (!$this->loggedInWithRole($role)) {
            // Create user (and project)
            $user = (object) [
                'name' => $this->getRandom()->name(8),
                'pass' => $this->getRandom()->name(16),
                'role' => $role,
            ];
            $user->mail = "{$user->name}@example.com";

            $this->testUser = $this->userCreate($user);

            $roles = explode(',', $role);
            $roles = array_map('trim', $roles);
            foreach ($roles as $role) {
                if (!in_array(strtolower($role), ['authenticated', 'authenticated user'])) {
                    // Only add roles other than 'authenticated user'.
                    $this->getDriver()->userAddRole($user, $role);
                }
            }

            // Login.
            $this->login($user);
        }
    }

    /**
     * @Given print login link
     */
    public function printLoginLink()
    {
        $testUser = user_load_by_name($this->testUser->name);
        if (!$testUser) {
            throw new \Exception('Test user not found.');
        }
        $uli = $this->getDriver('drush')->drush('uli', [
          parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH),
          "--name '" . $this->testUser->name . "'",
          "--browser=0",
        ]);
        print "Login link: " . trim($uli) . "\n";
    }

    /**
     * @When /^I wait for the file upload to complete$/
     */
    public function iWaitForTheFileUploadToComplete()
    {
        throw new PendingException();
        $this->getSession()->wait(5000, "document.querySelector('.file-upload-success') !== null");
    }
}
