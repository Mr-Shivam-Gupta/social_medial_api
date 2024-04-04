<x-app-layout>
    <div class=" messanger">
        <div class="messanger h-screen overflow-hidden bg-slate-500 ">
            <div class="flex">
                <div class="basis-2/6 border-r border-slate-100 bg-white pt-3">
                    <div class="search-box h-10 text-slate-300">
                        <div class="flex justify-between border-b border-slate-100 px-5 pb-1">
                            <form>
                                <i class="fa fa-search"></i>
                                <input type="search" class="font-light border-0 hover:border-0 focus:border-0 focus:ring-0 !shadow-none focus:!outline-none" placeholder="Search" />
                            </form>
                            <div>
                                <button class="relative">
                                    <i class="fa fa-message"></i>
                                    <i class="fa fa-plus absolute -top-2 text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                        <div class="user-list h-screen overflow-y-auto">
                    
                            <a  href="#" class="flex px-5 py-3 transition hover:cursor-pointer hover:bg-slate-100 justify-between ">
                                <div class="flex">
                                    <div class="pr-4">
                                        <img src="https://cdn-icons-png.flaticon.com/512/194/194938.png" alt="User" width="50" />
                                            {{-- <i class="fa fa-user-circle text-gray-300 text-5xl"></i> --}}
                                    </div>
                                    <div class="flex flex-col">
                                        <h3 class="text-md text-violet-500">user name</h3>
                                        <p class="h-5 overflow-hidden text-sm font-light text-gray-400">message</p>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <p><span class="text-green-500 text-sm"> spn</span> </p>
                                    <p><span class="text-green-500 text-sm">online</span></p> 
                                </div>
                            </a>
                        </div>
                </div>
                <div class="basis-4/6">
                    <div class="flex justify-center items-center h-screen">
                      <p class='font-bold text-gray-300 text-3xl'>Please select a user to chat...</p>
                    </div>
                    <div class="user-info-header bg-white px-5 py-3">
                        <div class="flex justify-between">
                            <div class="flex items-center">
                                <img src="https://cdn-icons-png.flaticon.com/512/194/194938.png" width="40" />
                                <h3 class="text-md pl-4 text-gray-400">Lupe Fiasco</h3>
                            </div>
                            <div>
                                <i class="fa fa-message text-violet-300"></i>
                                <i class="fa fa-video ml-3 text-gray-200"></i>
                                <i class="fa fa-phone ml-3 text-gray-200"></i>
                            </div>
                        </div>
                    </div>
                    <div class="messanger mt-4">
                        <div class="px-4">
                            <div class="receive-chat relative flex justify-start">
                                <div class="mb-2 max-w-[80%] rounded bg-violet-400 px-5 py-2 text-sm text-white">
                                    <i class="fa fa-caret-up absolute -top-2 text-violet-400"></i>
                                    <p>I got two tickets to go to see this awesome band called, Lorem ipsum dollar !! Do you want to come ?</p>
                                </div>
                            </div>
                            <div class="receive-chat relative flex justify-start">
                                <div class="mb-2 max-w-[80%] rounded bg-violet-400 px-5 py-2 text-sm text-white">
                                    <p>I got two tickets to go to see this awesome band called, Lorem ipsum dollar !! Do you want to come ?</p>
                                </div>
                            </div>

                            <div class="send-chat relative flex justify-end">
                                <div class="mb-2 max-w-[80%] rounded bg-violet-200 px-5 py-2 text-sm text-slate-500">
                                    <p>I got two tickets to go to see this awesome band called, Lorem ipsum dollar !! Do you want to come ?</p>
                                </div>
                            </div>

                            <div class="send-chat relative flex justify-end">
                                <div class="mb-2 max-w-[80%] rounded bg-violet-200 px-5 py-3 text-sm text-slate-500">
                                    <i class="fa fa-caret-down absolute bottom-0 right-4 text-violet-200"></i>
                                    <p>I got two tickets to go to see this awesome band called, Lorem ipsum dollar !! Do you want to come ?</p>
                                </div>
                            </div>

                        </div>
                        <div class="fixed bottom-0 w-full bg-gray-100 pl-4">
                            <textarea class="h-16 w-full overflow-y-auto bg-gray-100 pt-3 font-light border-0 hover:border-0 focus:border-0 focus:ring-0 !shadow-none focus:!outline-none" placeholder="Write a message"></textarea>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
