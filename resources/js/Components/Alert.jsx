import { usePage } from "@inertiajs/inertia-react";

const Alert = () => {
  let { alert } = usePage().props;

  const alertBgClass = () => {
    if (alert.type === "success") return "bg-green-500";
    if (alert.type === "error") return "bg-red-500";
    if (alert.type === "warning") return "bg-yellow-500";
    else return "bg-gray-700";
  };

  return (
    <>
      {alert.message && (
        <div
          className={`absolute top-0 p-4 text-center font-bold text-white w-full ${alertBgClass()}`}
        >
          <p>{alert.message}</p>
        </div>
      )}
    </>
  );
};

export default Alert;
