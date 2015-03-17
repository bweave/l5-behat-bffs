<?php

use App\Post;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Schema;
use Laracasts\Behat\Context\DatabaseTransactions;
use Laracasts\Behat\Context\Migrator;
use PHPUnit_Framework_Assert as PHPUnit;
use Faker\Factory as Faker;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    use Migrator, DatabaseTransactions;

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $email;

    /**
     * @var
     */
    public $resp;

    /**
     * @var
     */
    public $scope;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @When I register :name :email
     * @param $name
     * @param $email
     */
    public function iRegister($name, $email)
    {
        $this->name = $name;
        $this->email = $email;

        $this->visit('auth/register');
        $this->fillField('name', $name);
        $this->fillField('email', $email);
        $this->fillField('password', 'password');
        $this->fillField('password_confirmation', 'password');
        $this->pressButton('Register');
    }

    /**
     * @Then I should have an account
     */
    public function iShouldHaveAnAccount()
    {
        $this->assertSignedIn();
    }

    /**
     * @Given I have an account :name :email
     * @param $name
     * @param $email
     */
    public function iHaveAnAccount($name, $email)
    {
        $this->iRegister($name, $email);

        $this->visit('auth/logout');
    }

    /**
     * @When I sign in
     */
    public function iSignIn()
    {
        $this->visit('auth/login');

        $this->fillField('email', $this->email);
        $this->fillField('password', 'password');
        $this->pressButton('Login');
    }

    /**
     * @When I sign in with invalid creds
     */
    public function iSignInWithInvalidCreds()
    {
        $this->email = 'invalid@example.com';
        $this->iSignIn();
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn()
    {
        $this->assertSignedIn();
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn()
    {
        PHPUnit::assertTrue(Auth::guest());
        $this->assertPageAddress('auth/login');

        //$this->showLastResponse();
    }

    /**
     * Checks if the current user is logged in
     */
    private function assertSignedIn()
    {
        PHPUnit::assertTrue(Auth::check());
    }

    /**
     * @Given there are posts
     */
    public function thereArePosts()
    {
        $this->createPosts();
    }

    /**
     * @Given there are :numPosts posts
     * @param int $numPosts
     */
    public function createPosts($numPosts = 10)
    {
        $faker = Faker::create();

        foreach(range(1, $numPosts) as $i)
        {
            Post::create([
                'title' => "Post {$i}",
                'body' => $faker->paragraph(),
            ]);
        }
    }

    /**
     * @Given I request :verb :url
     * @param $verb
     * @param $url
     */
    public function iRequest($verb, $url)
    {
        $client = new Client(['base_url' => env('BASE_URL', 'http://localhost:8000')]);

        $this->resp = $client->$verb($url);
    }

    /**
     * @Then I get a :statusCode response
     * @param $statusCode
     */
    public function iGetAResponse($statusCode)
    {
        $code = (int) $this->resp->getStatusCode();
        PHPUnit::assertSame((int) $statusCode, $code);
    }

    /**
     * @Then scope into the first :scope property
     * @param $scope
     */
    public function scopeIntoTheFirstProperty($scope)
    {
        $this->scope = "{$scope}.0";
    }

    /**
     * @Then the properties exist:
     * @param PyStringNode $propertiesString
     */
    public function thePropertiesExist(PyStringNode $propertiesString)
    {
        foreach (explode("\n", (string) $propertiesString) as $property)
        {
            $this->thePropertyExists($property);
        }
    }

    /**
     * Checks that a property exists
     *
     * @param $property
     */
    public function thePropertyExists($property)
    {
        $payload = $this->getScopePayload();
        $message = sprintf(
            'Asserting the [%s] property exists in the scope [%s]: %s',
            $property,
            $this->scope,
            json_encode($payload)
        );
        if (is_object($payload))
        {
            PHPUnit::assertTrue(array_key_exists($property, get_object_vars($payload)), $message);
        }
        else
        {
            PHPUnit::assertTrue(array_key_exists($property, $payload), $message);
        }
    }

    /**
     * Returns the payload from the current scope within
     * the response.
     *
     * @return mixed
     */
    protected function getScopePayload()
    {
        $payload = $this->resp->json();
        if (! $this->scope)
        {
            return $payload;
        }

        return $this->arrayGet($payload, $this->scope);
    }

    /**
     * Get an item from an array using "dot" notation.
     *
     * @copyright   Taylor Otwell
     * @link        http://laravel.com/docs/helpers
     * @param       array   $array
     * @param       string  $key
     * @return      mixed
     */
    protected function arrayGet($array, $key)
    {
        if (is_null($key))
        {
            return $array;
        }
        foreach (explode('.', $key) as $segment)
        {
            if (is_object($array))
            {
                if (! isset($array->{$segment})) return;

                $array = $array->{$segment};
            }
            elseif (is_array($array))
            {
                if (! array_key_exists($segment, $array)) return;

                $array = $array[$segment];
            }
        }

        return $array;
    }
}
