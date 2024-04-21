import PostHeader from '@/Components/Posts/PostHeader';
import PostMedia from '@/Components/Posts/PostMedia';
import PostInteractions from '@/Components/Posts/PostInteractions';



export default function Post ({posts, auth, disabled = false}) {
    // console.log('Post',posts);
    return (
        <div>
            {posts.map(post => (
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5 p-5" key={post.id}>
                    <PostHeader post={post} disabled={disabled}/>
                    <PostMedia post={post} />
                    <PostInteractions post={post} auth={auth} disabled={disabled}/>
                </div>
            ))}
        </div>

    )
}
