<html lang="en">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>
   Dashboard
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
 </head>
 <body class="bg-gray-100 font-sans antialiased">
  <div class="flex">
   <!-- Sidebar -->
   <div class="w-64 bg-white h-screen shadow-md">
    <div class="flex items-center justify-center h-16 border-b">
     <div class="text-2xl font-bold text-purple-600">
      CL
     </div>
     <div class="ml-2 text-xl font-semibold">
      Codinglab
     </div>
    </div>
    <nav class="mt-10">
     <a class="flex items-center px-4 py-2 text-gray-700 bg-gray-100" href="#">
      <i class="fas fa-home">
      </i>
      <span class="ml-2">
       Dashboard
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="#">
      <i class="fas fa-file-alt">
      </i>
      <span class="ml-2">
       Content
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="statistique.php">
      <i class="fas fa-chart-bar">
      </i>
      <span class="ml-2">
       Analytics
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="#">
      <i class="fas fa-thumbs-up">
      </i>
      <span class="ml-2">
       Likes
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="#">
      <i class="fas fa-comments">
      </i>
      <span class="ml-2">
       Comments
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="#">
      <i class="fas fa-share">
      </i>
      <span class="ml-2">
       Share
      </span>
     </a>
     <a class="flex items-center px-4 py-2 mt-2 text-gray-600 hover:bg-gray-100" href="../user/logout.php">
      <i class="fas fa-sign-out-alt">
      </i>
      <span class="ml-2">
       Logout
      </span>
     </a>
     <!-- <div class="flex items-center px-4 py-2 mt-2 text-gray-600">
      <i class="fas fa-moon">
      </i>
      <span class="ml-2">
       Dark Mode
      </span>
      <label class="ml-auto inline-flex items-center cursor-pointer">
       <input class="sr-only" type="checkbox"/>
       <div class="w-10 h-4 bg-gray-300 rounded-full shadow-inner">
       </div>
       <div class="w-6 h-6 bg-white rounded-full shadow -ml-1 transform transition-transform duration-300">
       </div>
      </label>
     </div> -->
    </nav>
   </div>
   <!-- Main Content -->
   <div class="flex-1 p-6">
    <div class="flex items-center justify-between">
     <div class="relative">
      <input class="w-full px-4 py-2 pl-10 text-gray-700 bg-white border rounded-full focus:outline-none" placeholder="Search here..." type="text"/>
      <!-- <i class="absolute left-0 ml-3 text-gray-500 fas fa-search"> -->
      </i>
     </div>
     <img alt="User profile picture" class="w-10 h-10 rounded-full" src="https://placehold.co/40x40"/>
    </div>
    <div class="mt-6">
     <h2 class="text-2xl font-semibold">
      <i class="fas fa-tachometer-alt text-blue-500">
      </i>
      Dashboard
     </h2>
     <div class="grid grid-cols-1 gap-6 mt-6 sm:grid-cols-2 lg:grid-cols-3">
      <div class="p-6 bg-blue-100 rounded-lg shadow">
       <div class="flex items-center">
        <i class="text-3xl text-blue-500 fas fa-thumbs-up">
        </i>
        <div class="ml-4">
         <h3 class="text-lg font-semibold">
          Total Likes
         </h3>
         <p class="text-2xl font-bold">
          50,120
         </p>
        </div>
       </div>
      </div>
      <div class="p-6 bg-yellow-100 rounded-lg shadow">
       <div class="flex items-center">
        <i class="text-3xl text-yellow-500 fas fa-comments">
        </i>
        <div class="ml-4">
         <h3 class="text-lg font-semibold">
          Comments
         </h3>
         <p class="text-2xl font-bold">
          25,120
         </p>
        </div>
       </div>
      </div>
      <div class="p-6 bg-purple-100 rounded-lg shadow">
       <div class="flex items-center">
        <i class="text-3xl text-purple-500 fas fa-share">
        </i>
        <div class="ml-4">
         <h3 class="text-lg font-semibold">
          Total Share
         </h3>
         <p class="text-2xl font-bold">
          10,320
         </p>
        </div>
       </div>
      </div>
     </div>
    </div>
    <div class="mt-8">
     <h2 class="text-2xl font-semibold">
      <i class="fas fa-calendar-alt text-blue-500">
      </i>
      Recent Activity
     </h2>
     <div class="mt-4 overflow-x-auto">
      <table class="min-w-full bg-white rounded-lg shadow">
       <thead>
        <tr>
         <th class="px-4 py-2 text-left">
          Name
         </th>
         <th class="px-4 py-2 text-left">
          Email
         </th>
         <th class="px-4 py-2 text-left">
          Joined
         </th>
         <th class="px-4 py-2 text-left">
          Type
         </th>
         <th class="px-4 py-2 text-left">
          Status
         </th>
        </tr>
       </thead>
       <tbody>
        <tr>
         <td class="px-4 py-2 border-t">
          Prem Shahi
         </td>
         <td class="px-4 py-2 border-t">
          premshahi@gmail.com
         </td>
         <td class="px-4 py-2 border-t">
          2022-02-12
         </td>
         <td class="px-4 py-2 border-t">
          New
         </td>
         <td class="px-4 py-2 border-t">
          Liked
         </td>
        </tr>
        <tr>
         <td class="px-4 py-2 border-t">
          Deepa Chand
         </td>
         <td class="px-4 py-2 border-t">
          deepachand@gmail.com
         </td>
         <td class="px-4 py-2 border-t">
          2022-02-12
         </td>
         <td class="px-4 py-2 border-t">
          Member
         </td>
         <td class="px-4 py-2 border-t">
          Shared
         </td>
        </tr>
        <tr>
         <td class="px-4 py-2 border-t">
          Prakash Shahi
         </td>
         <td class="px-4 py-2 border-t">
          prakashshahi@gmail.com
         </td>
         <td class="px-4 py-2 border-t">
          2022-02-13
         </td>
         <td class="px-4 py-2 border-t">
          New
         </td>
         <td class="px-4 py-2 border-t">
          Liked
         </td>
        </tr>
        <tr>
         <td class="px-4 py-2 border-t">
          Manisha Chand
         </td>
         <td class="px-4 py-2 border-t">
          manishachan@gmail.com
         </td>
         <td class="px-4 py-2 border-t">
          2022-02-13
         </td>
         <td class="px-4 py-2 border-t">
          Member
         </td>
         <td class="px-4 py-2 border-t">
          Shared
         </td>
        </tr>
       </tbody>
      </table>
     </div>
    </div>
   </div>
  </div>
 </body>
</html>
