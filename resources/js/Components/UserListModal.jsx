import { Avatar } from "./CatalystComponents/avatar";
import { useState } from "react";
import { Link } from "./CatalystComponents/link";
import { Input } from "./CatalystComponents/input";
import { Label } from "./CatalystComponents/fieldset";
import { Button } from "./CatalystComponents/button";
import { Dialog, DialogActions, DialogBody, DialogDescription, DialogTitle } from './CatalystComponents/dialog';
import { Field } from "@headlessui/react";

export function UserListModal({buttonTitle= '', title = '', userList: initialUserList, followers, following}) {
  const [isOpen, setIsOpen] = useState(false)
  const [userList, setUserList] = useState(initialUserList);
  const [searchTerm, setSearchTerm] = useState('');
  const [usersFound, setUsersFound] = useState(true);

  const getUserFromItem = (item) => {
    if (followers && item.requester) return item.requester;
    else if (following && item.accepter) return item.accepter;
    else if (item.user) return item.user;
    else {
      return null;
    }
   
  }

  const userListDisplay = () => {
    return userList.map((item) => {
      const user = getUserFromItem(item);
      return (
        <li key={user.id}>
          <Link href={`/profile/${user.id}`} className="flex items-center">
            <Avatar initials={user.name.charAt(0)} src={user.profile_pic_url }className="mr-3 size-8" rounded/>
            <div>
              <strong>{user.name}</strong>
              <div className="text-gray-500">@{user.username}</div>
            </div>
          </Link>
        </li>
      );
    });
  }

  const handleUserSearch = (e) => {
    const value = e.target.value.toLowerCase();
    setSearchTerm(value);
    
    const filteredUsers = initialUserList
      .filter(item => {
        const user = getUserFromItem(item);
       
        if (user && user.name && user.username) {
          return user.name.toLowerCase().includes(value) || user.username.toLowerCase().includes(value);
        }
        return false;
      })
      .sort((a, b) => {
        const nameA = getUserFromItem(a).name.toLowerCase();
        const nameB = getUserFromItem(b).name.toLowerCase();
        return nameA.localeCompare(nameB);
      })
      .slice(0, 30);
  
    setUsersFound(filteredUsers.length > 0);
    setUserList(filteredUsers);
  }
  
  

  return (
    <>
      <Button type="button" onClick={() => setIsOpen(true)} plain>
        {buttonTitle}
      </Button>
      <Dialog open={isOpen} onClose={setIsOpen}>
        <DialogTitle>{title}</DialogTitle>
        <DialogBody>
          <Field className="flex items-baseline justify-center gap-3 mb-2">
            <Input name="search" placeholder="Search" type="text" onChange={handleUserSearch} value={searchTerm} />
            <Label className="self-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
              </svg>
            </Label>
          </Field>
          <ul className="h-60 overflow-y-auto">
            {usersFound ? 
              userListDisplay()
              : 
              <li>No users found</li>
            }
          </ul>
        </DialogBody>
      </Dialog>
    </>
  );
  
}
