import React from 'react';
import { InertiaLinkProps } from '@inertiajs/react';

export default function Card({ active = false, className = '', image = '', children, ...props }: InertiaLinkProps & { active: boolean, image: any }) {
    return (
        <>
            <div className={
                'max-w-sm bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700' +
                (active
                    ? 'border-indigo-400 text-gray-900 focus:border-indigo-700 '
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300 ')
                + className}>
                <div className="p-5">
                    {children}
                </div>
            </div>
        </>
    );
}

