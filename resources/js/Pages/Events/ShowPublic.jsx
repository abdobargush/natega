import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/inertia-react";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";

export default function EventsShowPublic({ event }) {
  const { data, post, setData, processing, errors, reset } = useForm({
    booked_at_date: "",
    booked_at_time: "",
    booker_name: "",
    booker_email: "",
  });

  const onHandleChange = (event) => {
    setData(
      event.target.name,
      event.target.type === "checkbox"
        ? event.target.checked
        : event.target.value
    );
  };

  const onHandleSubmit = (e) => {
    e.preventDefault();
    post(route("bookings.store", event), {
      onSuccess: () => reset(),
    });
  };

  const today = new Date();
  const availableFromDate = new Date(event.available_from_date);
  const minDate =
    today > availableFromDate
      ? today.toISOString().split("T")[0]
      : event.available_from_date;

  return (
    <AuthenticatedLayout hideNav={true}>
      <Head title={event.title} />

      <div className="-m-4 flex-1 md:flex items-stretch">
        <section className="flex-1 p-8">
          <div className="flex items-center mb-2">
            <img
              src={event.user.avatar}
              alt={event.user.name}
              className="h-8 w-8 rounded-full mr-2"
            />
            <h3 className="text-lg font-bold">{event.user.name}</h3>
          </div>
          <h1 className="text-2xl font-bold">{event.title}</h1>
          <span className="inline-flex items-center text-gray-600 font-semibold mt-1">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              strokeWidth={1.5}
              stroke="currentColor"
              className="w-5 h-5 mr-2"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            {event.duration} minutes
          </span>
          <p>{event.description}</p>
        </section>
        <section className="flex-1 p-8 border-t md:border-t-0 md:border-l border-gray-300">
          <h2 className="mb-2 font-bold text-xl">Book your slot</h2>

          <form action="" onSubmit={onHandleSubmit} className="space-y-4">
            <div>
              <InputLabel forInput="booked_at_date" value="Date" />

              <TextInput
                type="date"
                id="booked_at_date"
                name="booked_at_date"
                min={minDate}
                max={event.available_to_date}
                value={data.booked_at_date}
                className="mt-1 block w-full"
                handleChange={onHandleChange}
              />

              <InputError message={errors.booked_at_date} className="mt-2" />
            </div>

            <div>
              <InputLabel forInput="booked_at_time" value="Time" />

              <select
                name="booked_at_time"
                id="booked_at_time"
                value={data.booked_at_time}
                className="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
                onChange={onHandleChange}
              >
                <option value="">-- Select timeslot --</option>
                {event.timeslots.map((item, idx) => (
                  <option
                    key={idx}
                    value={item.start}
                  >{`${item.start} - ${item.end}`}</option>
                ))}
              </select>

              <InputError message={errors.booked_at_time} className="mt-2" />
            </div>

            <div>
              <InputLabel forInput="booker_name" value="Your Name" />

              <TextInput
                id="booker_name"
                name="booker_name"
                value={data.booker_name}
                className="mt-1 block w-full"
                handleChange={onHandleChange}
              />

              <InputError message={errors.booker_name} className="mt-2" />
            </div>

            <div>
              <InputLabel forInput="booker_email" value="Your Email" />

              <TextInput
                id="booker_email"
                type="email"
                name="booker_email"
                value={data.booker_email}
                className="mt-1 block w-full"
                handleChange={onHandleChange}
              />

              <InputError message={errors.booker_email} className="mt-2" />
            </div>

            <PrimaryButton className="w-full" disabled={processing}>
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
                  d="M4.5 12.75l6 6 9-13.5"
                />
              </svg>
              Confirm Booking
            </PrimaryButton>
          </form>
        </section>
      </div>
    </AuthenticatedLayout>
  );
}
