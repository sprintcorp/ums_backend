# USER MANAGEMENT API SERVICES  DEFINITION 


### Super Admin Services.
The following services that exist in the system is listed below. The services is grouped into entities which are 
`users`, `group`, `user-group`, `admin`, `super-admin`
<br>

1 . user signin
```bash
POST  /signin
```
Endpoint to signin using post method

2 . user signup
```bash
POST  /signup
```
Endpoint to signup using post method

3 . password reset  
```bash
POST   /password/reset
```

Endpoint to reset password using post method

4 . token activation/refresh
```bash
GET   /user/activate/{token}    
```
Endpoint to activate and refresh token

5 .  user update profile 
```bash
PATCH   /user/{user_id} 
```
Endpoint to update user profile using patch method

6 .  deleting user
```bash
DELETE   /users/{user_id}
```
Endpoint to delete user account using delete method

7 . fetching user group   
```bash
GET   /user/{user_id}/groups OR  /usergroups/{user_id}
```
Endpoint to see all the group a user belongs to using get method

8 . particular group details 
```bash
GET   /users/{user_id}/groups/{group_id}
```
Endpoint to check in the group they belong to using get method

9 . Register to a group.         
```bash
POST   /usersgroup
```
Endpoint to register to a group using post method

10 .  Exit  a user from a group  
```bash
DELETE   /usersgroup/{user_id}
```
Endpoint to exit a user from a group using delete method

11 . create group
```bash
POST  /groups 
```
Endpoint  to create a group

12 . Get all groups
```bash
GET  /groups
```
Endpoint to see all groups 

13 . Get a group details  
```bash
POST   /groups/{group_id}
```
Endpoint to check into a group

14 . Update a group details  
```bash
PATCH   /groups/{group_id}
```
Endpoint to check into a group

15 . delete a group  
```bash
DELETE   /groups/{group_id}
```
Endpoint to check into a group 

16 . get users and their respective groups.
```bash
GET  /usersgroups
```
Endpoint to see all users that belongs to group (and which group the belong to).

17 . get all users in a group. 
```bash
GET  /usersgroup/{group_id}
```
Endpoint to see all  users that belong to a group

18 . register user to group  
```bash
POST   /usersgroup
```
Endpoint  to register a user to a group
19 . get all users in a group. 
```bash
POST  /usersgroup
```
Endpoint to change the group a user belongs to. (if wrongly registered in a group).

20 . remove a user from a group  
```bash
DELETE  /usersgroup/{user_id}
```
Endpoint  to delete a user from a group

