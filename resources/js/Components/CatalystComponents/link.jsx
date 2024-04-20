/*
TODO: Update this component to use your client-side framework's link
component. We've provided examples of how to do this for Next.js,
Remix, and Inertia.js in the Catalyst documentation:

https://catalyst.tailwindui.com/docs#client-side-router-integration
*/

import { DataInteractive as HeadlessDataInteractive } from '@headlessui/react'
import React from 'react'
import { Link as InertiaLink } from '@inertiajs/react';

export const Link = React.forwardRef(function Link(props, ref) {
  return (
    <HeadlessDataInteractive>
      <InertiaLink {...props} ref={ref} />
    </HeadlessDataInteractive>
  )
})
