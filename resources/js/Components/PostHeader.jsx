import { Avatar } from "flowbite-react"
import { formatDistanceToNow, parseISO } from 'date-fns';

export default function PostHeader({ post }) {
    const formatDateTime = (dateTime) => {
        const dateTimeString = '2023-12-31T23:59:59Z';
        const dateTimeObject = parseISO(dateTimeString);
        return formatDistanceToNow(dateTimeObject, new Date());
    }

    return (
            <div className="flex items-center justify-between mb-2">
                <div className="flex items-center">
                    <Avatar className='mr-3' rounded />
                <div><strong>{post.user.name}</strong></div>
                </div>
                <div className="ml-auto">{formatDateTime(post.updated_at)} ago</div>
            </div>
    );
}
