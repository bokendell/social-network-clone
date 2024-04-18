import { Avatar } from "flowbite-react"
import { formatDistanceToNow, parseISO } from 'date-fns';

export default function PostHeader({ post }) {
    const formatDateTime = (dateTime) => {
        const dateTimeString = '2023-12-31T23:59:59Z';
        const dateTimeObject = parseISO(dateTimeString);
        return formatDistanceToNow(dateTimeObject, new Date());
    }

    return (
        <div className="flex mb-2">
            <Avatar rounded />
            <div className="flex ml-2 items-center justify-beteen">
                <div><strong>{post.user.name}</strong></div>
                <div>{formatDateTime(post.updated_at)} ago</div>
            </div>
        </div>
    )
}
