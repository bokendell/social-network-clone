import { Disclosure, Transition } from '@headlessui/react'
import { useState, useRef, useEffect } from 'react'
import { ChevronUpIcon } from '@heroicons/react/solid'
import { formatDistanceToNow, parseISO } from 'date-fns';
import { Avatar } from 'flowbite-react';
import pluralize from 'pluralize';

function CommentsDisclosure({ post, isOpen}) {
  const [inputValue, setInputValue] = useState('');
  const endOfCommentsRef = useRef(null);

  const scrollToBottom = () => {
    endOfCommentsRef.current?.scrollIntoView({ behavior: "smooth" });
  };

  useEffect(() => {
    scrollToBottom();
  }, [isOpen]);

  const formatDateTime = (dateTime) => {
    const dateTimeString = '2023-12-31T23:59:59Z';
    const dateTimeObject = parseISO(dateTimeString);
    return formatDistanceToNow(dateTimeObject, new Date());
}

  return (
    <Disclosure>
      {({ open }) => (
        <>
          <Disclosure.Button className="flex justify-between w-full py-2 text-sm font-medium text-left bg-white rounded-lg focus:outline-none focus-visible:ring focus-visible:ring-purple-500 focus-visible:ring-opacity-75">
            <span>view all {post.comments.length.toLocaleString('en-US')} {pluralize("comment", post.comments.length)}</span>
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
            <Disclosure.Panel className="pt-4 pb-2text-sm text-gray-500">
              {post.comments.map((comment) => (
                <div key={comment.id} className="flex mb-2 items-center space-x-2">
                  <Avatar className='self-start' rounded />
                  <div className="flex-1">
                    <span className=""><strong className='mr-2'>{comment.user.username}</strong>{comment.content}</span>
                    <div>{formatDateTime(comment.updated_at)} ago</div>
                  </div>
                </div>
              ))}
              <div ref={endOfCommentsRef} />
              <div className="mt-4">
                <form className="flex items-center">
                  <input
                    type="text"
                    value={inputValue}
                    onChange={(e) => setInputValue(e.target.value)}
                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    placeholder="Type your comment..."
                  />
                  <button
                    type="submit"
                    className="flex items-center justify-center p-2 ml-2 text-white bg-blue-500 rounded-md hover:bg-blue-600"
                  >
                    <ChevronUpIcon className="w-5 h-5" />
                  </button>
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
