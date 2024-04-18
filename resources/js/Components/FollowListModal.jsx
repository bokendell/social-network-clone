import { Button, Modal, Avatar } from "flowbite-react";
import { useState } from "react";
import { Link } from "@inertiajs/react";

export function FollowListModal({title, followList, followers, following}) {
  const [openModal, setOpenModal] = useState(false);

  return (
    <>
      <Button size="" color="white" onClick={() => setOpenModal(true)}>{title}</Button>
      <Modal show={openModal} onClose={() => setOpenModal(false)}>
        <Modal.Header>{title}</Modal.Header>
        <Modal.Body>
            <ul>
                {following && following === true && followList.data.map((user) => (
                    <li key={user.id}>
                        <Link href={`/profile/${user.accepter.id}`} className="flex items-center">
                            <Avatar className='mr-3' rounded />
                            <div>
                                <strong>{user.accepter.name}</strong>
                                <div className="text-gray-500">@{user.accepter.username}</div>
                            </div>
                        </Link>
                    </li>
                ))}
                {followers && followers === true && followList.data.map((user) => (
                    <li key={user.id}>
                        <Link href={`/profile/${user.requester.id}`} className="flex items-center">
                            <Avatar className='mr-3' rounded />
                            <div>
                                <strong>{user.requester.name}</strong>
                                <div className="text-gray-500">@{user.requester.username}</div>
                            </div>
                        </Link>
                    </li>
                ))}
            </ul>
        </Modal.Body>
      </Modal>
    </>
  );
}
