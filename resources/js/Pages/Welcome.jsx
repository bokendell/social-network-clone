import { Link, Head } from '@inertiajs/react';
import Header from '@/Components/Header';

export default function Welcome({ auth }) {


    return (
        <>
            <Head title="Home" />
            <Header auth={auth} />
        </>
    );
}
