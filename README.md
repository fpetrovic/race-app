1. `git clone git@github.com:fpetrovic/race-app.git`

2. `cd race-app`

3. `docker compose up --build -d`

4. Github CI pipeline is passing, but if you want to execute tests on local:
   `docker compose exec php ./vendor/bin/phpunit`

5. I created `./api/tests/output.csv` file that has 20k lines of data and that is used for automated testing

6. If you use app from the browser and then execute tests, tests will reload the db. In real life scenario,
   I would create test db, besides the dev db. 

7. I would also install phpstan and put the highest level for warnings

8. I would also update placements for RaceResults entity whenever `finishTime` is updated

9. I would also refactor RaceResults to be named RaceResult. 
