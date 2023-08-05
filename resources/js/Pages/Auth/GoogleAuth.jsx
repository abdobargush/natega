import PrimaryButton from "@/Components/PrimaryButton";
import GuestLayout from "@/Layouts/GuestLayout";
import { Head } from "@inertiajs/inertia-react";
import { Inertia } from "@inertiajs/inertia";

export default function GoogleAuth() {
  return (
    <GuestLayout>
      <Head title="Link Google Account" />

      <div className="text-center py-2">
        <h3 className="font-bold text-sm text-blue-500">Just one more step!</h3>
        <h1 className="font-bold text-2xl mt-1">Link Your Google Calendar</h1>
        <p className="mt-1">
          This will allow us to generate google meet links and automaticcly add
          events to your account.
        </p>
        <img
          src="/images/google-calendar.svg"
          alt="Google calendar icon"
          className="inline-block h-24 w-24"
        />
        <a
          href={route("google.redirect")}
          className="inline-flex w-full mt-4 items-center justify-center px-4 py-2 bg-blue-500 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-base text-white dark:text-gray-800 tracking-wide hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            strokeWidth="1.5"
            stroke="currentColor"
            className="w-5 h-5 mr-1"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"
            />
          </svg>
          Link Google Account
        </a>
      </div>
    </GuestLayout>
  );
}
