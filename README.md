# task_management_system_vue 
  - Please note that I used a different repository name since task_management_system exists in my GitHub.

In my case, I used docker to containerize them and utilize different ports due to other projects running on my device.
You may need to install docker if you don't have yet. Install Docker Desktop here https://docs.docker.com/desktop/install/windows-install/

# Setup Make Guide
 + git clone git@bitbucket.org:qjrincoywork/task_management_system_vue.git
 + cd task_management_system_vue
 + make setup-local

# Setup Manual Guide
 + git clone git@bitbucket.org:qjrincoywork/task_management_system_vue.git
 + cd task_management_system_vue/backend
 + docker-compose up --build -d
 + composer install
 + php artisan migrate

## NPM Install
 - cd task_management_system_vue/frontend
 - npm install
 - npm run dev


# API Documentation
[Task Management System Documentation](https://documenter.getpostman.com/view/14067001/2sB3BLhmpn)
