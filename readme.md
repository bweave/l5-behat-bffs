Laravel 5 & Behat are BFFs
--------------------------

This project is a quick automated testing demo with Laravel 5 & Behat.

### Points of Interest

- Uses Jeffrey Way's [Behat Laravel Extension](https://github.com/laracasts/Behat-Laravel-Extension)
    - ```behat.yml``` config
    - ```.env.behat``` for testing environment variable config
    - ```DatabaseTransactions``` trait for rolling back DB after each Scenario
    - ```Migrator``` trait for running migrations before each Scenario
- Reference material 
    - [Laravel 5 and Behat are BFFs](https://laracasts.com/lessons/laravel-5-and-behat-bffs)
    - [Laravel 5 and Behat Authentication](https://laracasts.com/lessons/laravel-5-and-behat-driving-authentication)
    - Phil Sturgeon's [Build APIs You Won't Hate](https://github.com/philsturgeon/build-apis-you-wont-hate)

### Tests

- ```features/dummy-check.feature```
    - simple dummy check to ensure Behat is working
    - tests that the default Laravel 5 homepage comes up
- ```features/auth.feature```
    - tests Laravel 5's out-of-the-box authentication and registration
- ```features/blog-post-api.feature```
    - tests the JSON repsonse from the endpoint ```/api/posts```
    - uses ```GuzzleHTTP``` to query the endpoint
    - uses helper methods from Phil Sturgeon's "Build APIs You Won't Hate" book
