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

Note/Heads up: I was not able to finish the frontend side. I focused mainly on the backend side for the API and code standards.
Thank you!

# API Documentation
[Task Management System Documentation](https://documenter.getpostman.com/view/14067001/2sB3BLhmpn)

## User Registration

<img width="1140" height="617" alt="user registration" src="https://github.com/user-attachments/assets/effad5ef-7c0c-4e87-a120-6da0f56536af" />

## User Login

<img width="1118" height="764" alt="user login" src="https://github.com/user-attachments/assets/d0985ca4-0188-4a72-86ce-acb450f8a86c" />

## Task List

<img width="1192" height="841" alt="task list" src="https://github.com/user-attachments/assets/ca34fbdf-fb8b-4996-8ac2-c25b52dc0669" />

## Task Create

<img width="1062" height="826" alt="task create" src="https://github.com/user-attachments/assets/190b8693-3f76-4111-b5cb-ae3f347a8a3e" />

## Task View

<img width="1091" height="656" alt="task view" src="https://github.com/user-attachments/assets/8e052db4-bee3-4ecd-8214-78423cbbc395" />

## Task Update

<img width="1065" height="426" alt="task update" src="https://github.com/user-attachments/assets/dc09ae25-fd58-4934-ba23-8c7147ca1a87" />

## Task Delete

<img width="1122" height="542" alt="task delete" src="https://github.com/user-attachments/assets/f8c06731-6dcc-43ef-8a77-9a6bd4f2e4a1" />

## Admin Dashboard

<img width="1128" height="624" alt="admin dashboard" src="https://github.com/user-attachments/assets/463409e7-657c-4ecf-94e7-fc3ad39e70d3" />

## Admin User Search

<img width="1323" height="802" alt="admin user search" src="https://github.com/user-attachments/assets/c63cbc18-f37c-4e72-ad32-4a52a28b000b" />

## Admin User Tasks

<img width="1106" height="797" alt="admin user tasks" src="https://github.com/user-attachments/assets/141b41c3-9a9c-475f-8775-329f997a4716" />
