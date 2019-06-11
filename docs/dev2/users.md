# USER MANAGEMENT API SERVICES  DEFINITION 

### Users Services  

##### This services uses the methods `GET` , `POST`, `PATCH`, `PATCH`, `DELETE` in consuming the UMS backend service.

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

10 .  Exit from a group  
```bash
DELETE   /usersgroup/{user_id}
```
Endpoint to exit a user from a group using delete method

