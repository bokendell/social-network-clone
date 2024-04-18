export default function Post ({post}) {
    return (
        <>
            <PostHeader post={post}/>
            <PostMedia post={post} />
            <PostInteractions post={post} />
        </>

    )
}
