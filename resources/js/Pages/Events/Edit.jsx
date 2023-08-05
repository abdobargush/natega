import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/inertia-react";
import PrimaryButton from "@/Components/PrimaryButton";
import EventForm from "@/Components/EventForm";

export default function EventsEdit({ event }) {
  const form = useForm({
    title: event.title,
    description: event.description,
    duration: event.duration,
    slug: event.slug,
    color: event.color,
    available_from_date: event.available_from_date,
    available_to_date: event.available_to_date,
    available_from_time: event.available_from_time,
    available_to_time: event.available_to_time,
  });

  const onHandleChange = (event) => {
    form.setData(
      event.target.name,
      event.target.type === "checkbox"
        ? event.target.checked
        : event.target.value
    );
  };

  const submit = (e) => {
    e.preventDefault();
    form.patch(route("events.update", event));
  };

  return (
    <AuthenticatedLayout>
      <Head title="Create Event" />

      <h1 className="font-bold text-3xl mb-6">Update Event</h1>
      <EventForm
        onHandleChange={onHandleChange}
        form={form}
        onHandleSubmit={submit}
      >
        <PrimaryButton className="text-lg" disabled={form.processing}>
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
          Update Event
        </PrimaryButton>
      </EventForm>
    </AuthenticatedLayout>
  );
}
