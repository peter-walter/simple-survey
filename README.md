# Peter's Simple Survey

## General Info
- Each question is stored as an entity in `src/Entity/Question`
- Questions are persisted via the `src/Repository/QuestionRespository`, using Session storage
- The survey entrypoint is `src/Controller/SurveyControler`, where the survey questions are seeded
- A form for each question is dynamically generated in `src/Controller/QuestionController`
- A summary of the responses are presented in `src/Controller/SummaryController`

## Features
- Questions can be single-choice, multi-choice or free-text
- Questions can be conditional on previous questions
- Questions pages can be revisited, persisting the previous value
  and can be edited from the summary screen

## Local development
To install pre-requisites
```
composer install
```

To spin up development server and launch in browser
```
symfony server:start -d
symfony open:local
```

To rebuild the CSS:
```
yarn install --frozen-lock-file;
yarn build; 
```

To run tests
```
bin/phpunit tests
```
