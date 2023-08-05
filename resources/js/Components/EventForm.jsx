import TextInput from "@/Components/TextInput";
import InputLabel from "@/Components/InputLabel";
import InputError from "@/Components/InputError";

function EventForm({ form, onHandleChange, onHandleSubmit, children }) {
  const { data, errors } = form;

  return (
    <form onSubmit={onHandleSubmit} className="space-y-4">
      <div>
        <InputLabel forInput="title" value="Title" />

        <TextInput
          id="title"
          name="title"
          value={data.title}
          className="mt-1 block w-full"
          handleChange={onHandleChange}
          isFocused={true}
        />

        <InputError message={errors.title} className="mt-2" />
      </div>

      <div>
        <InputLabel forInput="description" value="Description" />

        <textarea
          id="description"
          name="description"
          rows="3"
          value={data.description}
          className="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
          onChange={onHandleChange}
        ></textarea>

        <InputError message={errors.description} className="mt-2" />
      </div>

      <div>
        <InputLabel forInput="duration" value="Duration" />

        <select
          name="duration"
          id="duration"
          value={data.duration}
          className="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-blue-500 dark:focus:border-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 rounded-md shadow-sm"
          onChange={onHandleChange}
        >
          <option value="" selected>
            -- Select duration --
          </option>
          <option value="15">15</option>
          <option value="30">30</option>
          <option value="45">45</option>
          <option value="60">60</option>
        </select>

        <InputError message={errors.duration} className="mt-2" />
      </div>

      <div>
        <InputLabel forInput="slug" value="Slug" />

        <TextInput
          id="slug"
          name="slug"
          value={data.slug}
          className="mt-1 block w-full"
          handleChange={onHandleChange}
        />
        <p className="text-sm text-gray-600 mt-1">
          {route("events.show.public", "")}/<b>{data.slug}</b>
        </p>
        <InputError message={errors.slug} className="mt-2" />
      </div>

      <div>
        <InputLabel forInput="color" value="Color" />

        <div className="relative">
          <TextInput
            id="color"
            name="color"
            value={data.color}
            className="mt-1 block w-full pl-12"
            handleChange={onHandleChange}
          />

          <input
            type="color"
            name="color"
            value={data.color}
            className="absolute block h-8 w-8 left-2 top-1/2 -translate-y-1/2"
            onChange={onHandleChange}
          />
        </div>

        <InputError message={errors.color} className="mt-2" />
      </div>

      <div>
        <h4 className="font-bold text-lg ">Available Dates</h4>

        <div className="md:flex md:space-x-4">
          <div className="flex-1 mb-2">
            <InputLabel forInput="available_from_date" value="From" />

            <TextInput
              type="date"
              id="available_from_date"
              name="available_from_date"
              value={data.available_from_date}
              className="mt-1 block w-full"
              handleChange={onHandleChange}
            />

            <InputError message={errors.available_from_date} className="mt-2" />
          </div>
          <div className="flex-1 mb-2">
            <InputLabel forInput="available_to_date" value="To" />

            <TextInput
              type="date"
              id="available_to_date"
              name="available_to_date"
              value={data.available_to_date}
              className="mt-1 block w-full"
              handleChange={onHandleChange}
            />

            <InputError message={errors.available_to_date} className="mt-2" />
          </div>
        </div>
      </div>

      <div>
        <h4 className="font-bold text-lg">Available Timeslots</h4>

        <div className="md:flex md:space-x-4">
          <div className="flex-1 mb-2">
            <InputLabel forInput="available_from_time" value="From" />

            <TextInput
              type="time"
              id="available_from_time"
              name="available_from_time"
              value={data.available_from_time}
              className="mt-1 block w-full"
              handleChange={onHandleChange}
            />

            <InputError message={errors.available_from_time} className="mt-2" />
          </div>
          <div className="flex-1 mb-2">
            <InputLabel forInput="available_to_time" value="To" />

            <TextInput
              type="time"
              id="available_to_time"
              name="available_to_time"
              value={data.available_to_time}
              className="mt-1 block w-full"
              handleChange={onHandleChange}
            />

            <InputError message={errors.available_to_time} className="mt-2" />
          </div>
        </div>
      </div>

      {children}
    </form>
  );
}

export default EventForm;
