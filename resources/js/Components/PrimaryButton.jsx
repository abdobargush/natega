export default function PrimaryButton({
  type = "submit",
  className = "",
  disabled,
  children,
  onClick,
}) {
  return (
    <button
      type={type}
      onClick={onClick}
      className={
        `inline-flex items-center justify-center px-4 py-2 bg-blue-500 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-base text-white dark:text-gray-800 tracking-wide hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 ${
          disabled && "opacity-25"
        } ` + className
      }
      disabled={disabled}
    >
      {children}
    </button>
  );
}
