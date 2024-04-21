import { Head } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Avatar } from "@/Components/CatalystComponents/avatar";

export default function Blocked({ auth, user }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex">
                    <Avatar className='size-50 mr-3 self-start' initials={user.name.charAt(0)} src={auth.user.profile_pic_url} />
                    <div>
                        <h1 className="font-semibold text-2xl text-gray-800">{user.name}</h1>
                        <div className="text-gray-500">@{user.username}</div>
                    </div>
                </div>

            }
        >
            <Head title={`${user.name} (@${user.username})`} />
            <div className="flex flex-col items-center">
                <div className="text-xl font-semibold text-gray-800">You're blocked</div>
                <div className="text-gray-500">You can't follow or view @{user.username}'s posts</div>
            </div>
        </AuthenticatedLayout>
    );
}
