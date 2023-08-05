import ApplicationLogo from "@/Components/ApplicationLogo";
import Dropdown from "@/Components/Dropdown";
import { Link, usePage } from "@inertiajs/inertia-react";
import PrimaryButton from "@/Components/PrimaryButton";
import { Inertia } from "@inertiajs/inertia";
import Alert from "@/Components/Alert";

export default function Authenticated({ hideNav = false, children }) {
  const { auth } = usePage().props;

  return (
    <>
      <Alert />
      <div className="bg-blue-500">
        <div className="flex flex-col pt-0 pb-16 md:pb-24 md:pt-12 px-4 min-h-screen lg:max-w-screen-lg mx-auto">
          <nav className="h-24 flex items-center justify-between">
            <ApplicationLogo />

            <div className="sm:flex sm:items-center sm:ml-6">
              <div className="ml-3 relative">
                {auth && auth.user ? (
                  <Dropdown>
                    <Dropdown.Trigger>
                      <span className="inline-flex rounded-full">
                        <button
                          type="button"
                          className="inline-flex items-center px-3 py-2 border border-transparent font-medium rounded-full bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150"
                        >
                          <img
                            src={auth.user.avatar}
                            alt={auth.user.name}
                            className="h-6 w-6 rounded-full mr-2"
                          />
                          <span>{auth.user.name}</span>

                          <svg
                            className="ml-2 -mr-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                          >
                            <path
                              fillRule="evenodd"
                              d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                              clipRule="evenodd"
                            />
                          </svg>
                        </button>
                      </span>
                    </Dropdown.Trigger>

                    <Dropdown.Content>
                      <Dropdown.Link href={route("profile.edit")}>
                        Profile
                      </Dropdown.Link>
                      <Dropdown.Link
                        href={route("logout")}
                        method="post"
                        as="button"
                      >
                        Log Out
                      </Dropdown.Link>
                    </Dropdown.Content>
                  </Dropdown>
                ) : (
                  <Link
                    href={route("login")}
                    className="inline-flex items-center text-white"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      strokeWidth={1.5}
                      stroke="currentColor"
                      className="w-5 h-5 mr-1"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"
                      />
                    </svg>
                    Login
                  </Link>
                )}
              </div>
            </div>
          </nav>

          <main className="flex flex-col flex-1 bg-white p-4 rounded-md shadow-lg">
            {!hideNav && (
              <nav className="flex items-center justify-between border-b border-gray-300 -mx-4 px-4 pb-4 mb-6">
                <div className="flex space-x-4">
                  <Link
                    href={route("events.index")}
                    className={
                      route().current() === "events.index"
                        ? "relative font-bold  text-blue-500 after:absolute after:h-2 after:w-2 after:bg-blue-500 after:rounded-full after:-bottom-2 after:left-1/2 after:-translate-x-1/2"
                        : "text-gray-600 hover:text-gray-900"
                    }
                  >
                    Events
                  </Link>
                  <Link
                    href={route("bookings.index")}
                    className={
                      route().current() === "bookings.index"
                        ? "relative font-bold  text-blue-500 after:absolute after:h-2 after:w-2 after:bg-blue-500 after:rounded-full after:-bottom-2 after:left-1/2 after:-translate-x-1/2"
                        : "text-gray-600 hover:text-gray-900"
                    }
                  >
                    Bookings
                  </Link>
                </div>
                <PrimaryButton
                  className="bg-blue-500"
                  onClick={() => Inertia.visit(route("events.create"))}
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    strokeWidth={1.5}
                    stroke="currentColor"
                    className="w-5 h-5 mr-1"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      d="M12 4.5v15m7.5-7.5h-15"
                    />
                  </svg>
                  Add event
                </PrimaryButton>
              </nav>
            )}

            {children}
          </main>
        </div>
      </div>
    </>
  );
}
