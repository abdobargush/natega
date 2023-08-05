import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/inertia-react";
import PrimaryButton from "@/Components/PrimaryButton";
import EventForm from "@/Components/EventForm";

export default function EventsCreate(props) {
  const today = new Date();
  const tomorrow = new Date();
  tomorrow.setDate(today.getDate() + 1);

  const form = useForm({
    title: "",
    description: "",
    duration: "",
    slug: "",
    color: "#3b82f6",
    available_from_date: today.toISOString().split("T")[0],
    available_to_date: tomorrow.toISOString().split("T")[0],
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
    form.post(route("events.store"));
  };

  return (
    <AuthenticatedLayout auth={props.auth} errors={props.errors}>
      <Head title="Create Event" />

      <h1 className="font-bold text-3xl mb-6">Add New Event</h1>
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
              d="M12 4.5v15m7.5-7.5h-15"
            />
          </svg>
          Create New Event
        </PrimaryButton>
      </EventForm>
    </AuthenticatedLayout>
  );
}
