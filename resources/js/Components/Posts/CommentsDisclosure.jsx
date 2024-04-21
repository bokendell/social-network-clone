import { Disclosure, Transition } from '@headlessui/react'
import { useState, useRef, useEffect } from 'react'
import { ChevronUpIcon } from '@heroicons/react/solid'
import { formatDistanceToNow, parseISO } from 'date-fns';
import { Avatar } from '../CatalystComponents/avatar';
import { Link } from '@inertiajs/react';
import { Text, Strong } from '../CatalystComponents/text';
import { Input } from '../CatalystComponents/input';
import { Button } from '../CatalystComponents/button';
import axios from 'axios';
import pluralize from 'pluralize';

function CommentsDisclosure({ post: initialPost , isOpen, auth, disabled = false }) {
  const [inputValue, setInputValue] = useState('');
  const [post, setPost] = useState(initialPost);
  const endOfCommentsRef = useRef(null);
//   const scrollToBottom = () => {
//     endOfCommentsRef.current?.scrollIntoView({ behavior: "smooth" });
//   };

  const postComment = async (e) => {
    e.preventDefault();
    try {
        const response = await axios.post(`/feed/posts/${post.id}/comments`, {
            content: inputValue,
            post_id: post.id,
        });
        setInputValue('');
        setPost({
            ...post,
            comments: [...post.comments, response.data]
        });
        // scrollToBottom();
    } catch (error) {
        console.error("Error posting comment:", error);
        if (error.response) {
            console.error("Error status:", error.response.status);
            console.error("Error data:", error.response.data);
            alert(`Error posting comment: ${error.response.data.message}`);
        } else if (error.request) {
            console.error("No response:", error.request);
            alert("No response from server");
        } else {
            console.error("Error message:", error.message);
            alert("Error sending request");
        }
    }
  }

    const deleteComment = async (e, comment) => {
        e.preventDefault();
        try {
            const response = await axios.delete(`/feed/posts/${post.id}/comments/${comment.id}`);
            setPost({
                ...post,
                comments: post.comments.filter((c) => c.id !== comment.id)
            });
        } catch (error) {
            console.error("Error deleting comment:", error);
            if (error.response) {
                console.error("Error status:", error.response.status);
                console.error("Error data:", error.response.data);
                alert(`Error deleting comment: ${error.response.data.message}`);
            } else if (error.request) {
                console.error("No response:", error.request);
                alert("No response from server");
            } else {
                console.error("Error message:", error.message);
                alert("Error sending request");
            }
        }
    }

//   useEffect(() => {
//     scrollToBottom();
//   }, [isOpen]);

  const formatDateTime = (dateTime) => {
    const dateTimeObject = parseISO(dateTime);
    return formatDistanceToNow(dateTimeObject, new Date());
  }

  return (
    <Disclosure>
      {({ open }) => (
        <>
          <Disclosure.Button className="flex justify-between w-full py-2 font-medium text-left bg-white rounded-lg focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
            <Strong>view all {post.comments.length.toLocaleString('en-US')} {pluralize("comment", post.comments.length)}</Strong>
            <ChevronUpIcon
              className={`${open ? 'transform rotate-180' : ''} w-5 h-5`}
            />
          </Disclosure.Button>
          <Transition
            enter="transition duration-100 ease-out"
            enterFrom="transform scale-95 opacity-0"
            enterTo="transform scale-100 opacity-100"
            leave="transition duration-75 ease-out"
            leaveFrom="transform scale-100 opacity-100"
            leaveTo="transform scale-95 opacity-0"
          >
            <Disclosure.Panel className="pt-4 pb-2 text-sm">
              <div className="max-h-60 overflow-y-auto">
                {post.comments.map((comment) => (
                  <div key={comment.id} className="flex mb-2 items-center space-x-2">
                    <Link href={`/profile/${comment.user.id}`} className='self-start'><Avatar className="size-8" initials={auth.user.name.charAt(0)} src={auth.user.profile_pic_url} /></Link>
                    <div className="flex-1">
                      <Text><Strong className='mr-2'><Link href={`/profile/${comment.user.id}`}>{comment.user.username}</Link></Strong>{comment.content}</Text>
                      <div className='flex'>
                          <Strong className='text-xs mr-2'>{formatDateTime(comment.updated_at)} ago</Strong>
                          {comment.user.id === auth.user.id && (
                              <>
                                  <Button plain className="text-gray-500 hover:text-gray-900 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-4 h-4">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                  </Button>
                                  <Button plain onClick={(e) => deleteComment(e, comment)} className="text-gray-500 hover:text-red-600 focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-4 h-4">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                  </Button>
                              </>

                          )}
                      </div>

                    </div>
                  </div>
                ))}
              </div>
              <div ref={endOfCommentsRef} />
              <div className="mt-4">
                <form className="flex items-center" onSubmit={postComment}>
                  <Input
                    type="text"
                    value={inputValue}
                    onChange={(e) => setInputValue(e.target.value)}
                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-300 focus:ring focus:ring-gray-200 focus:ring-opacity-50"
                    placeholder="Type your comment..."
                  />
                  <Button
                    disabled={disabled}
                    type="submit"
                    className="flex items-center justify-center p-2 ml-2 hover:bg-gray-100 rounded-full focus:outline-none focus-visible:ring focus-visible:ring-gray-500 focus-visible:ring-opacity-75"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>

                  </Button>
                </form>
              </div>
            </Disclosure.Panel>
          </Transition>
        </>
      )}
    </Disclosure>
  );
}

export default CommentsDisclosure;
